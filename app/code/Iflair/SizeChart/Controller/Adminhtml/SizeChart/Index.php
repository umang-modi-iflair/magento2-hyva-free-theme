<?php
namespace Iflair\SizeChart\Controller\Adminhtml\SizeChart;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Iflair_SizeChart::size_chart';

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
        $resultPage->getConfig()->getTitle()->prepend(__('Size Units Details'));

        return $resultPage;
    }
}
