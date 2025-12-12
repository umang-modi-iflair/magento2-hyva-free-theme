<?php

namespace Newance\Training\Model\ResourceModel\Post;

/**
 * Training post collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * @var bool
     */
    protected $_previewFlag;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null|\Zend_Db_Adapter_Abstract $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->_date = $date;
        $this->_storeManager = $storeManager;
    }

    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Newance\Training\Model\Post', 'Newance\Training\Model\ResourceModel\Post');
        $this->_map['fields']['post_id'] = 'main_table.post_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
        $this->_map['fields']['category'] = 'category_table.category_id';
        $this->_map['fields']['brand'] = 'brand_table.brand_id';
    }

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id' || $field === 'store_ids') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add store filter to collection
     * @param array|int|\Magento\Store\Model\Store  $store
     * @param boolean $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store === null) {
            return $this;
        }

        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof \Magento\Store\Model\Store) {
                $this->_storeId = $store->getId();
                $store = [$store->getId()];
            }

            if (!is_array($store)) {
                $this->_storeId = $store;
                $store = [$store];
            }

            if (in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $store)) {
                return $this;
            }

            if ($withAdmin) {
                $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }

            $this->addFilter('store', ['in' => $store], 'public');
        }
        return $this;
    }

    /**
     * Add category filter to collection
     * @param array|int|\Newance\Training\Model\Category  $category
     * @return $this
     */
    public function addCategoryFilter($category)
    {
        if (!$this->getFlag('category_filter_added')) {
            if ($category instanceof \Newance\Training\Model\Category) {
                $category = [$category->getId()];
            }

            if (!is_array($category)) {
                $category = [$category];
            }

            $this->addFilter('category', ['in' => $category], 'public');
        }
        return $this;
    }

    /**
     * Add brand filter to collection
     * @param array|int|\Newance\Training\Model\Brand  $brand
     * @return $this
     */
    public function addBrandFilter($brand)
    {
        if (!$this->getFlag('brand_filter_added')) {
            if ($brand instanceof \Newance\Training\Model\Brand) {
                $brand = [$brand->getId()];
            }

            if (!is_array($brand)) {
                $brand = [$brand];
            }

            $this->addFilter('brand', ['in' => $brand], 'public');
        }
        return $this;
    }

    /**
     * Add is_active filter to collection
     * @return $this
     */
    public function addActiveFilter()
    {
        return $this
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('publish_time', ['gteq' => $this->_date->gmtDate()]);
    }

    /**
     * Add is_active and publish_time Less Than now
     * filter to collection
     *
     * @return $this
     */
    public function addPastFilter()
    {
        return $this
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('publish_time', ['lt' => $this->_date->gmtDate()]);
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('post_id');
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['cps' => $this->getTable('newance_training_post_store')])
                ->where('cps.post_id IN (?)', $items);
            $result = $connection->fetchPairs($select);

            if ($result) {
                foreach ($this as $item) {
                    $postId = $item->getData('post_id');
                    if (!isset($result[$postId])) {
                        continue;
                    }
                    if ($result[$postId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                    } else {
                        $storeId = $result[$item->getData('post_id')];
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_ids', [$result[$postId]]);
                }
            }

            if ($this->_storeId) {
                foreach ($this as $item) {
                    $item->setStoreId($this->_storeId);
                }
            }

            $select = $connection->select()->from(['cps' => $this->getTable('newance_training_post_category')])
                ->where('cps.post_id IN (?)', $items);
            $result = $connection->fetchAll($select);

            if ($result) {
                $categories = [];
                foreach ($result as $item) {
                    $categories[$item['post_id']][] = $item['category_id'];
                }

                foreach ($this as $item) {
                    $postId = $item->getData('post_id');
                    if (isset($categories[$postId])) {
                        $item->setData('categories', $categories[$postId]);
                    }
                }
            }

            $select = $connection->select()->from(['cps' => $this->getTable('newance_training_post_brand')])
                ->where('cps.post_id IN (?)', $items);
            $result = $connection->fetchAll($select);

            if ($result) {
                $brands = [];
                foreach ($result as $item) {
                    $brands[$item['post_id']][] = $item['brand_id'];
                }

                foreach ($this as $item) {
                    $postId = $item->getData('post_id');
                    if (isset($brands[$postId])) {
                        $item->setData('brands', $brands[$postId]);
                    }
                }
            }
        }

        $this->_previewFlag = false;
        return parent::_afterLoad();
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        foreach (['store', 'category', 'brand'] as $key) {
            if ($this->getFilter($key)) {
                $this->getSelect()->join(
                    [$key . '_table' => $this->getTable('newance_training_post_' . $key)],
                    'main_table.post_id = ' . $key . '_table.post_id',
                    []
                )->group(
                    'main_table.post_id'
                );
            }
        }
        parent::_renderFiltersBefore();
    }
}
