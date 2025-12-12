<?php

namespace Iflair\CountDownTimer\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class ProductOptions implements ArrayInterface
{
    protected $productCollectionFactory;

    public function __construct(CollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     */
    public function toOptionArray()
    {
        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('status', 1) // Only enabled products
            ->setPageSize(500); // Adjust limit as necessary

            $options = [
                ['value' => '', 'label' => __('-- Select Product --')]
            ];


            foreach ($collection as $product) {
            $options[] = [
                'value' => $product->getId(),
                'label' => $product->getName(),
            ];
        }

        // Sort by name for user convenience
        usort($options, function($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $options;
    }
}