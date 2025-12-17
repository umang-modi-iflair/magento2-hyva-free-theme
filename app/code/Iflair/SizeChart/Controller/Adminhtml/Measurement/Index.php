<?php
namespace Iflair\SizeChart\Controller\Adminhtml\Measurement;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Iflair_SizeChart::measurement';

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
        $resultPage->setActiveMenu('Iflair_SizeChart::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Measurement size Details'));

        return $resultPage;
    }
}
