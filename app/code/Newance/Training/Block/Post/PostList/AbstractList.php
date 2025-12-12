<?php

namespace Newance\Training\Block\Post\PostList;

/**
 * Abstract training post list block
 */
abstract class AbstractList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_post;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Newance\Training\Model\ResourceModel\Post\CollectionFactory
     */
    protected $_postCollectionFactory;

    /**
     * @var \Newance\Training\Model\ResourceModel\Post\Collection
     */
    protected $_postCollection;

    /**
     * @var \Newance\Training\Model\ResourceModel\Post\Collection
     */
    protected $_pastPostCollection;

    /**
     * @var \Newance\Training\Model\Url
     */
    protected $_url;

    /**
     * @var \Newance\Training\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Newance\Training\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $brandCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Newance\Training\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Newance\Training\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Newance\Training\Model\ResourceModel\Brand\CollectionFactory $brandCollectionFactory
     * @param \Newance\Training\Model\Url $url
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Newance\Training\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Newance\Training\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Newance\Training\Model\ResourceModel\Brand\CollectionFactory $brandCollectionFactory,
        \Newance\Training\Model\Url $url,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_postCollectionFactory = $postCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->_url = $url;
        $this->resourceConnection = $resourceConnection;
        $this->_date = $date;
    }

    /**
     * Get categories
     *
     * @return \Newance\Training\Model\ResourceModel\Category\Collection
     */
    public function getCategories()
    {
        // Create a connection to the database
        $connection = $this->resourceConnection->getConnection();
        // Get the category table name
        $catTable = $this->resourceConnection->getTableName('newance_training_category');
        // Get the post table name
        $postTable = $this->resourceConnection->getTableName('newance_training_post');
        // Get the post category association table name
        $postCategoryTable = $this->resourceConnection->getTableName('newance_training_post_category');

        // Select distinct Category IDs that have associated posts with a specific condition on publish_time
        $select = $connection->select()
            ->from(['c' => $catTable], 'category_id')
            ->joinLeft(['pc' => $postCategoryTable], 'c.category_id = pc.category_id', [])
            ->join(['p' => $postTable], 'pc.post_id = p.post_id', [])
            ->where('pc.post_id IS NOT NULL')
            ->where('p.publish_time > ?', $this->_date->gmtDate()) // Add the publish date filter
            ->distinct();

        // Execute the query and fetch cat IDs
        $catIds = $connection->fetchCol($select);

        $catData =  $this->categoryCollectionFactory->create()
            ->addFieldToFilter('category_id', ['in' => $catIds])
            ->addActiveCatFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId());

        return $catData;
    }

    /**
     * Get brands
     *
     * @return \Newance\Training\Model\ResourceModel\Brand\Collection
     */
    public function getBrands()
    {
        // Get the selected category ID
        $categoryId = $this->getRequest()->getParam('category');

        // Create a connection to the database
        $connection = $this->resourceConnection->getConnection();

        // Get the brand table name
        $brandTable = $this->resourceConnection->getTableName('newance_training_brand');

        // Get the post brand association table name
        $postBrandTable = $this->resourceConnection->getTableName('newance_training_post_brand');

        // Get the post table name
        $postTable = $this->resourceConnection->getTableName('newance_training_post');

        // Get the post category association table name
        $postCategoryTable = $this->resourceConnection->getTableName('newance_training_post_category');

        // Select distinct brand IDs that have associated posts
        $select = $connection->select()
            ->from(['b' => $brandTable], 'brand_id')
            ->joinLeft(['pb' => $postBrandTable], 'b.brand_id = pb.brand_id', [])
            ->join(['p' => $postTable], 'pb.post_id = p.post_id', [])
            ->distinct();

        // Add conditions based on category
        if ($categoryId) {
            $select->join(['pc' => $postCategoryTable], 'p.post_id = pc.post_id', [])
                ->where('pc.category_id = ?', $categoryId);
        } else {
            $select->where('p.publish_time > ?', $this->_date->gmtDate());
        }
        // Execute the query and fetch brand IDs
        $brandIds = $connection->fetchCol($select);

        // Load brands based on brand IDs
        $brands = $this->brandCollectionFactory->create()
            ->addFieldToFilter('brand_id', ['in' => $brandIds])
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId());

        return $brands;
    }

    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        $this->_postCollection = $this->_postCollectionFactory->create()
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'ASC');

        if (isset($_REQUEST['category']) && !empty($_REQUEST['category'])) {
            $this->_postCollection->addCategoryFilter($_REQUEST['category']);
        }

        if (isset($_REQUEST['brand']) && !empty($_REQUEST['brand'])) {
            $this->_postCollection->addBrandFilter($_REQUEST['brand']);
        }

        if ($this->getPageSize()) {
            $this->_postCollection->setPageSize($this->getPageSize());
        }
    }

    /**
     * Prepare past posts collection
     *
     * @return void
     */
    protected function _preparePastPostCollection()
    {
        $this->_pastPostCollection = $this->_postCollectionFactory->create()
            ->addPastFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'ASC');

        if ($this->getPageSize()) {
            $this->_pastPostCollection->setPageSize($this->getPageSize());
        }
    }

    /**
     * Prepare posts collection
     *
     * @return \Newance\Training\Model\ResourceModel\Post\Collection
     */
    public function getPostCollection()
    {
        if (is_null($this->_postCollection)) {
            $this->_preparePostCollection();
        }

        return $this->_postCollection;
    }

    /**
     * Get past posts collection
     *
     * @return \Newance\Training\Model\ResourceModel\Post\Collection
     */
    public function getPastPostCollection()
    {
        if (is_null($this->_pastPostCollection)) {
            $this->_preparePastPostCollection();
        }

        return $this->_pastPostCollection;
    }
}
