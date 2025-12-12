<?php
namespace Iflair\Faq\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Iflair\Faq\Model\ResourceModel\Faq\CollectionFactory;

class FaqListingDataProvider extends AbstractDataProvider
{
    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        
        $this->collection = $collectionFactory->create();
    }
    
    public function getData()
    {
        $collection = $this->getCollection();
        $items = $collection->getItems();
        $loadedData = [];
        $serialNumber = 1;

        foreach ($items as $product) {
            $data = $product->getData();
            $data['sr_no'] = $serialNumber++;
            $loadedData[$product->getId()] = $data;
        }

        return [
            'totalRecords' => $collection->getSize(),
            'items' => array_values($loadedData),
        ];
    }
}
