<?php

namespace Newance\Training\Block\Sidebar;

use Magento\Store\Model\ScopeInterface;

/**
 * Training sidebar categories block
 */
class Categories extends \Magento\Framework\View\Element\Template
{
    use Widget;

    /**
     * @var string
     */
    protected $_widgetKey = 'categories';

    /**
     * @var \Newance\Training\Model\ResourceModel\Category\Collection
     */
    protected $_categoryCollection;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Newance\Training\Model\ResourceModel\Category\Collection $categoryCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Newance\Training\Model\ResourceModel\Category\Collection $categoryCollection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_categoryCollection = $categoryCollection;
    }

    /**
     * Get grouped categories
     * @return \Newance\Training\Model\ResourceModel\Category\Collection
     */
    public function getGroupedChilds()
    {
        $k = 'grouped_childs';
        if (!$this->hasData($k)) {
            $array = $this->_categoryCollection
                ->addActiveCatFilter()
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->setOrder('position')
                ->getTreeOrderedArray();

            $this->setData($k, $array);
        }

        return $this->getData($k);
    }

    /**
     * Retrieve block identities
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Block::CACHE_TAG . '_training_categories_widget'];
    }

    /**
     * Retrieve true if need to show posts count
     * @return int
     */
    public function showPostsCount()
    {
        $key = 'show_posts_count';

        if (!$this->hasData($key)) {
            $this->setData($key, (bool)$this->_scopeConfig->getValue(
                'newance_training/sidebar/' . $this->_widgetKey . '/show_posts_count',
                ScopeInterface::SCOPE_STORE
            ));
        }

        return $this->getData($key);
    }
}
