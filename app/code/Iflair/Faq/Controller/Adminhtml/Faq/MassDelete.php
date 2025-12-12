<?php

namespace Iflair\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Iflair\Faq\Model\ResourceModel\Faq\CollectionFactory;

class MassDelete extends Action
{
    protected $collectionFactory;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $faqIds = $this->getRequest()->getParam('selected');

        if (!is_array($faqIds) || empty($faqIds)) {
            $this->messageManager->addErrorMessage(__('Invalid Faq ID(s).'));
            return $resultRedirect->setPath('*/*/index');
        }                                                                           

        try {
            $collection = $this->collectionFactory->create()->addFieldToFilter('faq_id', ['in' => $faqIds]);
            foreach ($collection as $employee) {
                $employee->delete();
            }
            $this->messageManager->addSuccessMessage(__('Successfully deleted %1 FAQ(s).', count($faqIds)));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error: ' . $e->getMessage()));
        }                                                   

        return $resultRedirect->setPath('*/*/index');
    }
}
