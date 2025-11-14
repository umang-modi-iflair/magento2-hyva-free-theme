<?php

namespace Newance\Training\Controller\Request;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptchaUi\Model\RequestHandlerInterface;

class Form extends \Magento\Framework\App\Action\Action
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $state
     * @param RequestHandlerInterface $requestHandler
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        RequestHandlerInterface $requestHandler
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->requestHandler = $requestHandler;

        parent::__construct($context);
    }

    /**
     * Check if is IDIS
     *
     * @return bool
     */
    public function isIdisGlobal()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();

        //idisglobal.be
        if (strpos($baseUrl, 'idisglobal.be') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Execute sends the form data via email
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
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

            //retrieve posted form data
            $postName = $this->getRequest()->getPostValue('name');
            $postEmail = $this->getRequest()->getPostValue('email');
            $postTele = $this->getRequest()->getPostValue('phone');
            $postBedrijf = $this->getRequest()->getPostValue('company');
            $postBtwNummer = $this->getRequest()->getPostValue('vat');
            $postBericht = $this->getRequest()->getPostValue('message');

            //get selected email template ID from configuration
            $selectedTemplateId = $this->scopeConfig->getValue(
                'newance_training/request_form/request_email_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $toEmail = $this->scopeConfig->getValue(
                'newance_training/request_form/request_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $toName = $this->scopeConfig->getValue(
                'newance_training/request_form/request_admin_identity',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            //prepare template variables
            $templateVars = [
                'store_title' => $this->storeManager->getStore()->getBaseUrl(),
                'naam' => $postName,
                'email' => $postEmail,
                'telefoonnummer' => $postTele,
                'bedrijf' => $postBedrijf,
                'btw-nummer' => $postBtwNummer,
                'bericht' => $postBericht
            ];

            if (empty($postName) || empty($postEmail) || empty($postTele)) {
                throw new \Exception("Invalid form data: Missing required fields.");
            }

            //get current store ID
            $storeId = $this->storeManager->getStore()->getId();
            $this->inlineTranslation->suspend();

            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];

            $from = [
                'email' => 'no-reply@distri-company.com',
                'name' => 'Distri-Company'
            ];

            if ($this->isIdisGlobal()) {
                $from = [
                    'email' => 'no-reply@idisglobal.be',
                    'name' => 'IDIS Global'
                ];
            }

            //build email transport
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($selectedTemplateId)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail, $toName)
                ->addCc('webshop@distri-company.com', 'Webshop Distri-Company')
                ->getTransport();

            $transport->sendMessage();

            $this->messageManager->addSuccess(__('Bedankt voor je een aanvraag. We contacteren je zo snel mogelijk.'));
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Error sending email: %1', $e->getMessage()));
        }

        //prepare and return redirection to the previous page
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
