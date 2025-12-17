<?php
namespace Iflair\SizeChart\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Iflair\SizeChart\Model\ResourceModel\Measurement\CollectionFactory;
use Magento\Framework\App\RequestInterface;

class AddMeasurementDataProvider extends AbstractDataProvider
{
    protected $collection;
    protected $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        $id = (int)$this->request->getParam('measurement_id');

        if (!$id) {
            return [];
        }

        $item = $this->collection->getItemById($id);

        if (!$item) {
            return [];
        }

        return [
            $id => $item->getData()
        ];

        // echo '<pre>';
        //     echo $item->getData();
        //     die("Test");
        // echo '</pre>';
    }
}
