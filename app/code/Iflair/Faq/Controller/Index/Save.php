<?php
namespace Iflair\Faq\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Iflair\Faq\Model\FaqFactory;
use Iflair\Faq\Model\SendMail;

class Save extends Action
{
    protected $faqFactory;
    protected $sendMail;
    public function __construct(
        Context $context,
        FaqFactory $faqFactory,
        SendMail $sendMail
    ) {
        $this->faqFactory = $faqFactory;
        $this->sendMail = $sendMail;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            $this->messageManager->addErrorMessage(__('Invalid form submission.'));
            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        try {
            $faq = $this->faqFactory->create();
            $faq->setProductId($data['product_id']); 
            $faq->setCustomerName($data['customer_name']);
            $faq->setCustomerEmail($data['customer_email']);
            $faq->setQuestion($data['question']);
            $faq->setAnswer('');
            $faq->save();

            $this->sendMail->sendFaqEmail(
                $data['customer_email'],
                $data['customer_name'],
                $data['question'],
                "Thank you for your question. Our team will respond shortly."
            );


            $this->messageManager->addSuccessMessage(__('Thank you! Your question has been submitted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Sorry, something went wrong. Please check exception.log for details.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}