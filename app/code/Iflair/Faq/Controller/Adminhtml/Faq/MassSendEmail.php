<?php

namespace Iflair\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Iflair\Faq\Model\SendMail;
use Psr\Log\LoggerInterface;

class MassSendEmail extends Action
{
    protected $faqCollectionFactory;
    protected $sendMail;
    protected $logger;

    const ADMIN_RESOURCE = 'Iflair_Faq::faq_send_email';

    /**
     * @param Action\Context $context
     * @param \Iflair\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory
     * @param SendMail $sendMail
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        \Iflair\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
        SendMail $sendMail,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->faqCollectionFactory = $faqCollectionFactory;
        $this->sendMail = $sendMail;
        $this->logger = $logger;
    }

    /**
     * Execute mass email action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $faqIds = $this->getRequest()->getParam('selected');

        if (!is_array($faqIds) || empty($faqIds)) {
            $this->messageManager->addErrorMessage(__('Please select FAQ(s) to send email.'));
            return $resultRedirect->setPath('*/*/index');
        }

        $collection = $this->faqCollectionFactory->create()
            ->addFieldToFilter('faq_id', ['in' => $faqIds]);

        $sentCount = 0;
        $failedIds = [];

        foreach ($collection as $faq) {
            $email = $faq->getCustomerEmail();


            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $failedIds[] = $faq->getId();
                $this->logger->warning('Invalid email for FAQ ID ' . $faq->getId());
                $this->messageManager->addErrorMessage(
                    __('Email failed for FAQ ID %1: Invalid or missing email address.', $faq->getId())
                );
                continue;
            }

            try {
                $this->sendMail->sendFaqEmail(
                    $email,
                    $faq->getCustomerName(),
                    $faq->getQuestion(),
                    $faq->getAnswer()
                );
                $sentCount++;
            } catch (\Exception $e) {
                $failedIds[] = $faq->getId();
                
                $errorMessage = $e->getMessage();
                
                $this->logger->error(
                    'Failed to send FAQ email for ID ' . $faq->getId() . ': ' . $errorMessage,
                    ['exception' => $e]
                );
                
                $this->messageManager->addErrorMessage(
                    __('Email failed for FAQ ID %1. Error: %2', $faq->getId(), $errorMessage)
                );
                
            }
        }

        if ($sentCount) {
            $this->messageManager->addSuccessMessage(__('Successfully sent email(s) for %1 FAQ(s).', $sentCount));
        }

        if (!empty($failedIds)) {
            $this->messageManager->addErrorMessage(
                __('Summary: Failed to send email for %1 FAQ(s). Check the detailed error messages above and the exception log.', count($failedIds))
            );
        }

        return $resultRedirect->setPath('*/*/index');
    }
    
    /**
     * Check permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}