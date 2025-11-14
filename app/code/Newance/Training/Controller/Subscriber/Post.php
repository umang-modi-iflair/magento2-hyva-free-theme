<?php

namespace Newance\Training\Controller\Subscriber;

use Laminas\Validator\NotEmpty;
use Laminas\Validator\EmailAddress;
use Magento\Framework\App\ObjectManager;
use Magento\ReCaptchaUi\Model\RequestHandlerInterface;
use Magento\Framework\App\Request\DataPersistorInterface;

class Post extends \Magento\Framework\App\Action\Action
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Newance\Training\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Newance\Training\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Framework\Escaper $escaper
     * @param RequestHandlerInterface $requestHandler
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Newance\Training\Model\SubscriberFactory $subscriberFactory,
        \Magento\Framework\Escaper $escaper,
        RequestHandlerInterface $requestHandler
    ) {
        parent::__construct($context);

        $this->_transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->subscriberFactory = $subscriberFactory;
        $this->_escaper = $escaper;
        $this->requestHandler = $requestHandler;
    }

    /**
     * Init Post
     *
     * @return \Newance\Training\Model\Post || false
     */
    protected function _initPost()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();

        $post = $this->_objectManager->create('Newance\Training\Model\Post')->load($id);

        if (!$post->isVisibleOnStore($storeId)) {
            return false;
        }

        $post->setStoreId($storeId);

        return $post;
    }

    /**
     * Post subscriber
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        //build post
        $post = $this->_initPost();
        if (!$post) {
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        try {
            //Google reCAPTCHA Server Side Validation
            $key = "subscriber_form";
            $request = $this->getRequest();
            $response = $this->getResponse();

            $redirectOnFailureUrl = $this->_redirect->getRedirectUrl();
            $this->requestHandler->execute($key, $request, $response, $redirectOnFailureUrl);

            //spam check
            if (!empty($this->getRequest()->getPostValue('website'))) {
                throw new \Exception("Marked as spam.");
            }

            //fetch POST data from request
            $data = $this->getRequest()->getPostValue();

            //if empty POST > redirect to trainingpost
            if (!$data) {
                $this->_redirect($post->getPostUrl());
                return;
            }

            $error = false;

            $nameValidator = new NotEmpty();
            $emailValidator = new EmailAddress();

            if (!$nameValidator->isValid(trim($data['name']))) {
                $error = true;
            }

            if (!$emailValidator->isValid(trim($data['email']))) {
                $error = true;
            }

            if ($error) {
                throw new \Exception();
            }

            if (empty($data['name']) || empty($data['email']) || empty($data['phone'])) {
                throw new \Exception("Invalid form data: Missing required fields.");
            }

            if (strlen($data['name']) > 40) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The name cannot exceed 40 characters.')
                );
            }

            if (preg_match('/(http|www|\.com)/i', $data['name'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Invalid name')
                );
            }

            //get selected email template ID from configuration
            $templateId = $this->scopeConfig->getValue(
                'newance_training/post_view/subscribers/sender_email_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $templateAdminId = $this->scopeConfig->getValue(
                'newance_training/post_view/subscribers/sender_email_template_admin',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            //update training, downgrade free subscriptions
            $training = $this->_objectManager->create('Newance\Training\Model\Post')->load($post->getId());
            $training->setRemainingPlaces($training->getRemainingPlaces() - 1);
            $training->save();

            //create new subscriber in DB
            $subscriber = $this->subscriberFactory
                ->create()
                ->setData($data)
                ->setPostId($post->getId())
                ->save();

            //some vars
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $storeId = $this->_storeManager->getStore()->getId();

            //send email to subscriber (thank you e-mail)
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ]
                )
                ->setTemplateVars(
                    [
                        'subscriber_name' => $this->_escaper->escapeHtml($subscriber->getName()),
                        'post_title' => $post->getTitle(),
                        'post_date' => $post->getPublishDate('d/m/Y'),
                        'post_hour' => $post->getHour(),
                        'post_location' => $post->getLocation(),
                        'post_url' => $post->getPostUrl(),
                    ]
                )
                ->setFrom($this->scopeConfig->getValue('newance_training/post_view/subscribers/sender_email_identity', $storeScope))
                ->addCc('webshop@distri-company.com', 'Webshop Distri-Company')
                ->addTo($data['email'])
                ->getTransport();

            $transport->sendMessage();

            //send email to admin
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($templateAdminId)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ]
                )
                ->setTemplateVars(
                    [
                        'subscriber_name' => $this->_escaper->escapeHtml($subscriber->getName()),
                        'subscriber_email' => $this->_escaper->escapeHtml($subscriber->getEmail()),
                        'subscriber_phone' => $this->_escaper->escapeHtml($subscriber->getPhone()),
                        'subscriber_company' => $this->_escaper->escapeHtml($subscriber->getCompany()),
                        'subscriber_vat' => $this->_escaper->escapeHtml($subscriber->getVat()),
                        'subscriber_message' => nl2br($this->_escaper->escapeHtml($subscriber->getMessage())),
                        'post_url' => $post->getPostUrl(),
                        'post_title' => $post->getTitle(),
                    ]
                )
                ->setFrom($this->scopeConfig->getValue('newance_training/post_view/subscribers/sender_email_identity', $storeScope))
                ->addTo($this->scopeConfig->getValue('newance_training/post_view/subscribers/recipient_email', $storeScope))
                ->setReplyTo($data['email'])
                ->getTransport();

            $transport->sendMessage();

            //add success message
            $this->messageManager->addSuccess(__('Thank you for your subscription.'));

            //clear data persistor
            $this->getDataPersistor()->clear('subscriber');
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t process your subscription right now. Sorry, that\'s all we know. (Error: ' . $e->getMessage() . ')'));
            $this->getDataPersistor()->set('subscriber', $data ?? []);
        }

        //always redirect back to the article
        $this->_redirect($post->getPostUrl());
        return;
    }

    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }
}
