<?php
namespace Newance\Training\Controller\Adminhtml\Post;

/**
 * Training post related products controller
 */
class RelatedProducts extends \Newance\Training\Controller\Adminhtml\Post
{
    /**
     * View related products action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $model = $this->_getModel();
        $this->_getRegistry()->register('current_model', $model);

        $this->_view->loadLayout()
            ->getLayout()
            ->getBlock('training.post.edit.tab.relatedproducts')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));

        $this->_view->renderLayout();
    }
}
