<?php
namespace Iflair\SizeChart\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Iflair\SizeChart\Model\ResourceModel\SizeChart\CollectionFactory;
use Magento\Framework\App\RequestInterface;

class AddUnitDataProvider extends AbstractDataProvider
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
        $id = (int)$this->request->getParam('sizeunit_id');

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
    }
}
