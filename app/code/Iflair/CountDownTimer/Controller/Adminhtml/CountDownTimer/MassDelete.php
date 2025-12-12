<?php

namespace Iflair\CountDownTimer\Controller\Adminhtml\CountDownTimer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Iflair\CountDownTimer\Model\ResourceModel\Widget\CollectionFactory;

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
        $widgetIds = $this->getRequest()->getParam('selected');

        if (!is_array($widgetIds) || empty($widgetIds)) {
            $this->messageManager->addErrorMessage(__('Invalid Widget ID(s).'));
            return $resultRedirect->setPath('*/*/index');
        }                                                                           

        try {
            $collection = $this->collectionFactory->create()->addFieldToFilter('widget_id', ['in' => $widgetIds]);
            foreach ($collection as $employee) {
                $employee->delete();
            }
            $this->messageManager->addSuccessMessage(__('Successfully deleted %1 widget.', count($widgetIds)));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error: ' . $e->getMessage()));
        }                                                   

        return $resultRedirect->setPath('*/*/index');
    }
}
