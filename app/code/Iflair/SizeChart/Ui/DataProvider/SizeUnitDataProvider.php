<?php
namespace Iflair\SizeChart\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Iflair\SizeChart\Model\ResourceModel\SizeChart\CollectionFactory;

class SizeUnitDataProvider extends AbstractDataProvider
{
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        $collection = $this->getCollection();

        $items = $collection->getItems();
        $loadedData = [];
        $serialNumber = 1;

        foreach ($items as $product) {
            $data = $product->getData();

            // echo '<pre>';
            //     echo print_r($data);
            //     die("test");
            // echo '</pre>';

            $data['sr_no'] = $serialNumber++;
            $loadedData[$product->getId()] = $data;
        }

        return [
            'totalRecords' => $collection->getSize(),
            'items' => array_values($loadedData),
        ];
    }
}

