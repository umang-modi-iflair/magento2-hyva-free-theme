<?php

namespace Iflair\Delivery\Controller\Adminhtml\Delivery;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Iflair\Delivery\Model\ResourceModel\Delivery\CollectionFactory;

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
        $deliveryIds = $this->getRequest()->getParam('selected');

        if (!is_array($deliveryIds) || empty($deliveryIds)) {
            $this->messageManager->addErrorMessage(__('Invalid Delivery ID(s).'));
            return $resultRedirect->setPath('*/*/index');
        }                                                                           

        try {
            $collection = $this->collectionFactory->create()->addFieldToFilter('id', ['in' => $deliveryIds]);
            foreach ($collection as $employee) {
                $employee->delete();
            }
            $this->messageManager->addSuccessMessage(__('Successfully deleted %1 Delivery(s).', count($deliveryIds)));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error: ' . $e->getMessage()));
        }                                                   

        return $resultRedirect->setPath('*/*/index');
    }
}
