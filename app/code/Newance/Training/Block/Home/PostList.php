<?php

namespace Newance\Training\Block\Home;

/**
 * Training home posts list
 */
class PostList extends \Newance\Training\Block\Post\PostList\AbstractList
{
    /**
     * Retrieve post html
     *
     * @param  \Newance\Training\Model\Post $post
     * @return string
     */
    public function getPostHtml($post)
    {
        return $this->getChildBlock('training.home.posts.list.item')
            ->setPost($post)
            ->toHtml();
    }

    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        $pageSize = $this->_scopeConfig->getValue(
            'newance_training/post_list/posts_per_page_home',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->setPageSize($pageSize);
        parent::_preparePostCollection();
    }
}
