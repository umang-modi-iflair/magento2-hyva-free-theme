<?php
namespace Iflair\SizeChart\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Iflair\SizeChart\Model\ResourceModel\Template\CollectionFactory;

class TemplateDataProvider extends AbstractDataProvider
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

        foreach ($items as $product) {
            $data = $product->getData();

            // echo '<pre>';
            //     echo print_r($data);
            //     die("test");
            // echo '</pre>';

            $loadedData[$product->getId()] = $data;
        }

        return [
            'totalRecords' => $collection->getSize(),
            'items' => array_values($loadedData),
        ];
    }
}

