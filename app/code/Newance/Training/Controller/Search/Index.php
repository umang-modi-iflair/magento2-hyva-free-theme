<?php
namespace Newance\Training\Controller\Search;

/**
 * Training search results view
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * View training search results action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
