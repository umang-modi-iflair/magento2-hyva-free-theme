<?php
namespace Iflair\Delivery\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Iflair\Delivery\Model\ResourceModel\Delivery\CollectionFactory;
use Magento\Framework\App\ResourceConnection;

class DeliveryDataProvider extends AbstractDataProvider
{
    protected $resource;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        ResourceConnection $resource,        
        array $meta = [],//
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
        $this->resource   = $resource;    
    }

    public function getData()
    {   
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('pincode_delivery_info');

        $result = $connection->fetchAll("SELECT * FROM $table ORDER BY id ASC");
        // $serialNumber = 1;

        $now = new \DateTime();
        $cutoffTime = (clone $now)->setTime(15, 0); 

        foreach ($result as &$item) {
            // $item['serial_number'] = $serialNumber++;

            $item['is_serviceable'] = (int)$item['is_serviceable'];
            $item['cod_available'] = (int)$item['cod_available'];
        }

        return [
            'totalRecords' => count($result),
            'items' => $result
        ];
    }
}
