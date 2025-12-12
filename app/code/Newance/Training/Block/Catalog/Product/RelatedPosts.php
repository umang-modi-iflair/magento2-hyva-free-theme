<?php

namespace Newance\Training\Block\Catalog\Product;

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
            'newance_training/product_page/number_of_related_posts',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$pageSize) {
            $pageSize = 5;
        }
        $this->setPageSize($pageSize);

        parent::_preparePostCollection();

        $product = $this->getProduct();
        $this->_postCollection->getSelect()->joinLeft(
            ['rl' => $product->getResource()->getTable('newance_training_post_relatedproduct')],
            'main_table.post_id = rl.post_id',
            ['position']
        )->where(
            'rl.related_id = ?',
            $product->getId()
        );
    }

    /**
     * Retrieve true if Display Related Posts enabled
     * @return boolean
     */
    public function displayPosts()
    {
        return (bool) $this->_scopeConfig->getValue(
            'newance_training/product_page/related_posts_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve posts instance
     *
     * @return \Newance\Training\Model\Category
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData(
                'product',
                $this->_coreRegistry->registry('current_product')
            );
        }
        return $this->getData('product');
    }

    /**
     * Get Block Identities
     * @return Array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG . '_training_relatedposts_' . $this->getPost()->getId()];
    }
}
