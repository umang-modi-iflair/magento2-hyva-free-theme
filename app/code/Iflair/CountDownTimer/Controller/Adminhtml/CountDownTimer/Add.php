<?php
namespace Iflair\CountDownTimer\Controller\Adminhtml\CountDownTimer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Add extends Action
{
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
        $resultPage->setActiveMenu('Iflair_CountDownTimer::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Add Countdown Widgets List'));
        return $resultPage;
    }
}

