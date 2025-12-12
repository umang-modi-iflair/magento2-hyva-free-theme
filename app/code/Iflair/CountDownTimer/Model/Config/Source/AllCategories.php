<?php

namespace Iflair\CountDownTimer\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class AllCategories implements ArrayInterface
{
    protected $categoryCollectionFactory;

    public function __construct(CollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('name');

        $options = [
            ['value' => '', 'label' => __('-- Select Categories --')]
        ];

        foreach ($collection as $category) {
            $options[] = [
                'label' => $category->getName(),
                'value' => $category->getId()
            ];
        }
        return $options;
    }
}
