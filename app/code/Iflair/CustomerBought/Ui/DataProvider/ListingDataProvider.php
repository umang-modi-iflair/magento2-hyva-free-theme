<?php
namespace Iflair\CustomerBought\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Iflair\CustomerBought\Model\ResourceModel\CustomerBought\CollectionFactory;

class ListingDataProvider extends AbstractDataProvider
{
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
        $items = $this->collection->getItems();
        $data = [];

        foreach ($items as $item) {
            $data[] = $item->getData();
        }

        // echo '<pre>';
        //     echo print_r($data);
        //     die("Test");
        // echo '</pre>';

        return [
            'totalRecords' => $this->collection->getSize(),
            'items' => $data
        ];
    }
}
