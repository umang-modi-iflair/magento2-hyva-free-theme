<?php

namespace Newance\Training\Block\Brand;

use Magento\Framework\View\Element\Template;
use Newance\Training\Model\BrandFactory;

/**
 * Training Brandlogo block
 */
class Brandlogo extends Template
{

    /**
     * @var \Newance\Training\Model\BrandFactory
     */
    protected $brandFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Newance\Training\Model\BrandFactory $brandFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */

    public function __construct(
        \Newance\Training\Model\Post $post,
        \Newance\Training\Model\PostFactory $postFactory,
        \Magento\Framework\Registry $coreRegistry,
        Template\Context $context,
        BrandFactory $brandFactory,
        array $data = []
    ) {
        $this->_postFactory = $postFactory;
        $this->_post = $post;
        $this->_coreRegistry = $coreRegistry;
        $this->brandFactory = $brandFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve post instance
     *
     * @return \Newance\Training\Model\Post
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
     * Retrieve brand image logo
     *
     * @return \Newance\Training\Model\BrandFactory
     */
    public function getBrandImageData()
    {
        $brandIds = $this->getPost()->getData('brands');
        $brandCollection = $this->brandFactory->create()->getCollection()
            ->addFieldToFilter('brand_id', ['in' => $brandIds]);
        $brandData = $brandCollection->getFirstItem();
        return $brandData;
    }
}
