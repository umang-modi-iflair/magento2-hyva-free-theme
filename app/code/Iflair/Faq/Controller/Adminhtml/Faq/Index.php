<?php
namespace Iflair\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Iflair_Faq::iflair_countdowntimer';

    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        
        $resultPage->setActiveMenu('Iflair_Faq::iflair_countdowntimer');
        
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Questions'));
        return $resultPage;
    }
}
