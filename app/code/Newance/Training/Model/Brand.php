<?php

namespace Newance\Training\Model;

/**
 * Brand model
 *
 * @method \Newance\Training\Model\ResourceModel\Brand _getResource()
 * @method \Newance\Training\Model\ResourceModel\Brand getResource()
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
 */
class Brand extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Brand's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Base media folder path
     */
    const BASE_MEDIA_PATH = 'newance_training_brand';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'newance_training_brand';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'training_brand';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Newance\Training\Model\ResourceModel\Post\CollectionFactory
     */
    protected $postCollectionFactory;

    /**
     * Initialize dependencies.
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param Url                                                          $url
     * @param \Newance\Training\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Url $url,
        \Newance\Training\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_url = $url;
        $this->postCollectionFactory = $postCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Newance\Training\Model\ResourceModel\Brand');
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Brand' : 'Brands';
    }

    /**
     * Retrieve true if brand is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available brand statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Check if brand identifier exist for specific store
     * return brand id if brand exists
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
     * Retrieve parent brand ids
     * @return array
     */
    public function getParentIds()
    {
        $k = 'parent_ids';
        if (!$this->hasData($k)) {
            $this->setData(
                $k,
                $this->getPath() ? explode('/', $this->getPath()) : []
            );
        }

        return $this->getData($k);
    }

    /**
     * Retrieve parent brand id
     * @return array
     */
    public function getParentId()
    {
        if ($pIds = $this->getParentIds()) {
            return $pIds[count($pIds) - 1];
        }
        return 0;
    }

    /**
     * Retrieve parent brand
     * @return self || false
     */
    public function getParentBrand()
    {
        $k = 'parent_brand';
        if (!$this->hasData($k)) {
            if ($pId = $this->getParentId()) {
                $brand = clone $this;
                $brand->load($pId);

                if ($brand->getId()) {
                    $this->setData($k, $brand);
                }
            }
        }

        if ($brand = $this->getData($k)) {
            if ($brand->isVisibleOnStore($this->getStoreId())) {
                return $brand;
            }
        }

        return false;
    }

    /**
     * Check if current brand is parent brand
     * @param  self  $brand
     * @return boolean
     */
    public function isParent($brand)
    {
        if (is_object($brand)) {
            $brand = $brand->getId();
        }

        return in_array($brand, $this->getParentIds());
    }

    /**
     * Retrieve children brand ids
     * @return array
     */
    public function getChildrenIds()
    {
        $k = 'children_ids';
        if (!$this->hasData($k)) {
            $brands = \Magento\Framework\App\ObjectManager::getInstance()
                ->create($this->_collectionName);

            $ids = [];
            foreach ($brands as $brand) {
                if ($brand->isParent($this)) {
                    $ids[] = $brand->getId();
                }
            }

            $this->setData(
                $k,
                $ids
            );
        }

        return $this->getData($k);
    }

    /**
     * Check if current brand is child brand
     * @param  self  $brand
     * @return boolean
     */
    public function isChild($brand)
    {
        return $brand->isParent($this);
    }

    /**
     * Retrieve brand depth level
     * @return int
     */
    public function getLevel()
    {
        return count($this->getParentIds());
    }

    /**
     * Retrieve catgegory url route path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_BRAND);
    }

    /**
     * Retrieve brand url
     * @return string
     */
    public function getBrandUrl()
    {
        return $this->_url->getUrl($this, URL::CONTROLLER_BRAND);
    }

    /**
     * Retrieve brand image url
     *
     * @return string|false
     */
    public function getBrandImage()
    {
        if (!$this->hasData('brand_image')) {
            if ($file = $this->getData('brand_img')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('brand_image', $image);
        }

        return $this->getData('brand_image');
    }

    /**
     * Retrieve banner image url
     *
     * @return string|false
     */
    public function getBannerImage()
    {
        if (!$this->hasData('brand_image')) {
            if ($file = $this->getData('banner_img')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('brand_image', $image);
        }

        return $this->getData('brand_image');
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
     * Retrieve number of posts in this brand
     *
     * @return int
     */
    public function getPostsCount()
    {
        $key = 'posts_count';
        if (!$this->hasData($key)) {
            $brands = $this->getChildrenIds();
            $brands[] = $this->getId();
            $posts = $this->postCollectionFactory->create()
                ->addActiveFilter()
                ->addStoreFilter($this->getStoreId())
                ->addBrandFilter($brands);
            $this->setData($key, (int)$posts->getSize());
        }

        return $this->getData($key);
    }
}
