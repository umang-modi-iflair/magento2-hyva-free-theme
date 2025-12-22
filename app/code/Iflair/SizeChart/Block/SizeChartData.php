<?php

namespace Iflair\SizeChart\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Iflair\SizeChart\Model\ResourceModel\Template\CollectionFactory;

class SizeChartData extends Template
{
    protected $_registry;
    protected $collectionFactory;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get the current product
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Get size chart data dynamically via collection
     */
    public function getSizeChartData()
{
    $collection = $this->collectionFactory->create();
    $collection->addFieldToFilter('status', 1);
    $collection->setOrder('created_at', 'ASC');

    $data = [];
    foreach ($collection as $item) {
        $data[] = $item->toArray(); 
    }

        return $data;
    }
}
