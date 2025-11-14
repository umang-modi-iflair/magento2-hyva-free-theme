<?php
namespace Newance\Training\Controller\Adminhtml\Post;

/**
 * Training post related posts controller
 */
class RelatedPosts extends \Newance\Training\Controller\Adminhtml\Post
{
    /**
     * View related posts action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $model = $this->_getModel();
        $this->_getRegistry()->register('current_model', $model);

        $this->_view->loadLayout()
            ->getLayout()
            ->getBlock('training.post.edit.tab.relatedposts')
            ->setPostsRelated($this->getRequest()->getPost('posts_related', null));

        $this->_view->renderLayout();
    }
}
