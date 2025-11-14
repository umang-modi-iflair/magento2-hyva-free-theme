<?php

namespace Newance\Training\Block\Post\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Training post related posts block
 */
class RelatedPosts extends \Newance\Training\Block\Post\PostList\AbstractList
{
    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        $pageSize = (int) $this->_scopeConfig->getValue(
            'newance_training/post_view/related_posts/number_of_posts',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_postCollection = $this->getPost()->getRelatedPosts()
            ->addActiveFilter()
            ->setPageSize($pageSize ?: 5);

        $this->_postCollection->getSelect()->order('rl.position', 'ASC');
    }

    /**
     * Retrieve true if Display Related Posts enabled
     * @return boolean
     */
    public function displayPosts()
    {
        return (bool) $this->_scopeConfig->getValue(
            'newance_training/post_view/related_posts/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve posts instance
     *
     * @return \Newance\Training\Model\Category
     */
    public function getPost()
    {
        if (!$this->hasData('post')) {
            $this->setData(
                'post',
                $this->_coreRegistry->registry('current_training_post')
            );
        }
        return $this->getData('post');
    }

    /**
     * Get Block Identities
     * @return Array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_training_relatedposts_' . $this->getPost()->getId()];
    }
}
