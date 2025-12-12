<?php
namespace Iflair\CountDownTimer\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Iflair\CountDownTimer\Model\ResourceModel\Widget\CollectionFactory;

class ListingDataProvider extends AbstractDataProvider
{
    protected $collection;
    protected $loadedData;

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

    /**
     * Return data for form
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
    
        $items = $this->collection->getItems();
        $this->loadedData = [];
    
        foreach ($items as $item) {
            $data = $item->getData();
    
            if (!empty($data['category_ids']) && is_string($data['category_ids'])) {
                $data['category_ids'] = array_values(
                    array_filter(explode(',', $data['category_ids']))
                );
            }
    
            if (empty($data['product_id'])) {
                $data['product_id'] = null;
            }
    
            if (empty($data['display_on'])) {
                $data['display_on'] = null;
            } else {
                if (is_string($data['display_on'])) {
                    $data['display_on'] = array_values(
                        array_filter(explode(',', $data['display_on']))
                    );
                }
            }
    
            $this->loadedData[$item->getId()] = $data;
        }
    
        return $this->loadedData;
    }
    
    
}
