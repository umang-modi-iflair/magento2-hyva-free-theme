<?php
namespace Newance\Training\Model;

/**
 * Post model
 *
 * @method \Newance\Training\Model\ResourceModel\Post _getResource()
 * @method \Newance\Training\Model\ResourceModel\Post getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getMetaKeywords()
 * @method $this setMetaKeywords(string $value)
 * @method string getMetaDescription()
 * @method $this setMetaDescription(string $value)
 * @method string getIdentifier()
 * @method $this setIdentifier(string $value)
 * @method string getContent()
 * @method $this setContent(string $value)
 * @method string getContentHeading()
 * @method $this setContentHeading(string $value)
 */
class Post extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Posts's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Base media folder path
     */
    const BASE_MEDIA_PATH = 'newance_training';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'newance_training_post';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'training_post';

    /**
     * @var \Newance\Training\Model\Url
     */
    protected $_url;

    /**
     * @var \Newance\Training\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var \Newance\Training\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $_brandCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Newance\Training\Model\ResourceModel\Category\Collection
     */
    protected $_parentCategories;

    /**
     * @var \Newance\Training\Model\ResourceModel\Brand\Collection
     */
    protected $_parentBrands;

    /**
     * @var \Newance\Training\Model\ResourceModel\Post\Collection
     */
    protected $_relatedPostsCollection;

    /**
     * @var \Newance\Training\Model\ResourceModel\Subscriber\CollectionFactory
     */
    protected $_subscriberCollectionFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Newance\Training\Model\Url $url
     * @param \Newance\Training\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Newance\Training\Model\ResourceModel\Brand\CollectionFactory $brandCollectionFactory
     * @param \Newance\Training\Model\ResourceModel\Subscriber\CollectionFactory $subscriberCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Url $url,
        \Newance\Training\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Newance\Training\Model\ResourceModel\Brand\CollectionFactory $brandCollectionFactory,
        \Newance\Training\Model\ResourceModel\Subscriber\CollectionFactory $subscriberCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_url = $url;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_brandCollectionFactory = $brandCollectionFactory;
        $this->_subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_relatedPostsCollection = clone($this->getCollection());
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Newance\Training\Model\ResourceModel\Post');
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Post' : 'Posts';
    }

    /**
     * Retrieve true if post is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available post statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Check if post identifier exist for specific store
     * return post id if post exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Retrieve post url route path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_POST);
    }

    /**
     * Retrieve post url
     * @return string
     */
    public function getPostUrl()
    {
        return $this->_url->getUrl($this, URL::CONTROLLER_POST);
    }

    /**
     * Retrieve subscriber post url
     *
     * @return string
     */
    public function getPostSubscriberUrl()
    {
        return $this->_url->getUrl($this, URL::CONTROLLER_SUBSCRIBER);
    }

    public function getFeaturedImage()
    {
        if (!$this->hasData('featured_image')) {
            if ($file = $this->getData('featured_img')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('featured_image', $image);
        }

        return $this->getData('featured_image');
    }

    public function getHomeImage()
    {
        if (!$this->hasData('home_image')) {
            if ($file = $this->getData('home_img')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('home_image', $image);
        }

        return $this->getData('home_image');
    }

    /**
     * Retrieve post parent categories
     * @return \Newance\Training\Model\ResourceModel\Category\Collection
     */
    public function getParentCategories()
    {
        if (is_null($this->_parentCategories)) {
            $this->_parentCategories = $this->_categoryCollectionFactory->create()
                ->addFieldToFilter('category_id', ['in' => $this->getCategories()])
                ->addStoreFilter($this->getStoreId())
                ->addActiveFilter()
                ->setOrder('position');
        }

        return $this->_parentCategories;
    }

    /**
     * Retrieve post parent categories count
     * @return int
     */
    public function getCategoriesCount()
    {
        return count($this->getParentCategories());
    }

    /**
     * Retrieve post parent brands
     * @return \Newance\Training\Model\ResourceModel\Brand\Collection
     */
    public function getParentBrands()
    {
        if (is_null($this->_parentBrands)) {
            $this->_parentBrands = $this->_brandCollectionFactory->create()
                ->addFieldToFilter('brand_id', ['in' => $this->getBrands()])
                ->addStoreFilter($this->getStoreId())
                ->addActiveFilter()
                ->setOrder('position');
        }

        return $this->_parentBrands;
    }

    /**
     * Retrieve post parent brands count
     * @return int
     */
    public function getBrandsCount()
    {
        return count($this->getParentBrands());
    }

    /**
     * Retrieve post related posts
     * @return \Newance\Training\Model\ResourceModel\Post\Collection
     */
    public function getRelatedPosts()
    {
        if (!$this->hasData('related_posts')) {
            $collection = $this->_relatedPostsCollection
                ->addFieldToFilter('post_id', ['neq' => $this->getId()])
                ->addStoreFilter($this->getStoreId());
            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('newance_training_post_relatedpost')],
                'main_table.post_id = rl.related_id',
                ['position']
            )->where(
                'rl.post_id = ?',
                $this->getId()
            );
            $this->setData('related_posts', $collection);
        }

        return $this->getData('related_posts');
    }

    /**
     * Retrieve post related products
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getRelatedProducts()
    {
        if (!$this->hasData('related_products')) {
            $collection = $this->_productCollectionFactory->create();

            if ($this->getStoreId()) {
                $collection->addStoreFilter($this->getStoreId());
            }

            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('newance_training_post_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            )->where(
                'rl.post_id = ?',
                $this->getId()
            );

            $this->setData('related_products', $collection);
        }

        return $this->getData('related_products');
    }

    /**
     * Retrieve if is visible on store
     * @return bool
     */
    public function isVisibleOnStore($storeId)
    {
        return $this->getIsActive() && array_intersect([0, $storeId], $this->getStoreIds());
    }

    /**
     * Retrieve post publish date using format
     * @param  string $format
     * @return string
     */
    public function getPublishDate($format = 'd/m/Y H:i')
    {
        return date($format, strtotime($this->getData('publish_time')));
    }

    /**
     * Retrieve post subscribers
     *
     * @return \Newance\Training\Model\ResourceModel\Subscriber\Collection
     */
    public function getSubscribers()
    {
        if (!$this->hasData('subscribers')) {
            $collection = $this->_subscriberCollectionFactory->create()
                ->addFieldToFilter('post_id', ['eq' => $this->getId()])
                ->addActiveFilter();

            $this->setData('subscribers', $collection);
        }

        return $this->getData('subscribers');
    }
}
