<?php
namespace Iflair\CustomerBought\Controller\Adminhtml\CustomerBought;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Iflair_CustomerBought::customer_bought';

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
        $resultPage->setActiveMenu('Iflair_CustomerBought::customer_bought');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Bundle Packs List'));
        return $resultPage;
    }
}
