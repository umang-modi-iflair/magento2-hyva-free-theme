<?php

namespace Newance\Training\Block\Sidebar;

use Magento\Store\Model\ScopeInterface;

/**
 * Training sidebar brands block
 */
class Brands extends \Magento\Framework\View\Element\Template
{
    use Widget;

    /**
     * @var string
     */
    protected $_widgetKey = 'brands';

    /**
     * @var \Newance\Training\Model\ResourceModel\Brand\Collection
     */
    protected $_brandCollection;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Newance\Training\Model\ResourceModel\Brand\Collection $brandCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Newance\Training\Model\ResourceModel\Brand\Collection $brandCollection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_brandCollection = $brandCollection;
    }

    /**
     * Get grouped brands
     * @return \Newance\Training\Model\ResourceModel\Brand\Collection
     */
    public function getGroupedChilds()
    {
        $k = 'grouped_childs';
        if (!$this->hasData($k)) {
            $array = $this->_brandCollection
                ->addActiveFilter()
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
        return [\Magento\Cms\Model\Block::CACHE_TAG . '_training_brands_widget'];
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
