<?php
namespace Iflair\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry; // <-- 1. Include Registry interface

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Iflair_Faq::iflair_countdowntimer';

    protected $resultPageFactory;
    protected $_faqFactory;
    protected $_coreRegistry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Iflair\Faq\Model\FaqFactory $faqFactory,
        Registry $coreRegistry 
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_faqFactory = $faqFactory;
        $this->_coreRegistry = $coreRegistry;
    }

    public function execute()
    {
        $faqId = $this->getRequest()->getParam('faq_id');
        $model = $this->_faqFactory->create();

        if ($faqId) {
            $model->load($faqId);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This FAQ no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_coreRegistry->register('faq_data', $model);

        $resultPage = $this->resultPageFactory->create();
        
        $resultPage->setActiveMenu('Iflair_Faq::iflair_countdowntimer');
        
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Edit'));
        return $resultPage;
    }
}