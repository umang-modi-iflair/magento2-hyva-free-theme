<?php
namespace Newance\Training\Controller\Index;

/**
 * Training home page view
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * View training homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
