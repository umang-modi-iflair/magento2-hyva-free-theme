<?php
declare(strict_types=1);

namespace BluethinkInc\AddCustomTab\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class GridTab extends Action
{
    protected $_pageFactory;

    public function __construct(Action\Context $context, PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Tabs'));
        $resultPage->setActiveMenu("BluethinkInc_AddCustomTab::popup");
        return $resultPage;
    }

    // bypass ACL to prevent 404
    protected function _isAllowed()
    {
        return true;
    }
}
