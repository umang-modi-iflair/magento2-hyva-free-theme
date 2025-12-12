<?php
namespace Iflair\Faq\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Iflair\Faq\Model\ResourceModel\Faq\CollectionFactory;

class FaqDataProvider extends AbstractDataProvider
{
    protected $loadedData = [];

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
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        foreach ($this->collection->getItems() as $faq) {
            $data = $faq->getData();

            if (isset($data['store_view']) && $data['store_view'] !== null && $data['store_view'] !== '') {
                if (is_string($data['store_view'])) {
                    $parts = array_filter(array_map('trim', explode(',', $data['store_view'])), function ($v) {
                        return $v !== '';
                    });
                    $data['store_view'] = array_values(array_map('intval', $parts));
                } elseif (is_numeric($data['store_view'])) {
                    $data['store_view'] = [(int)$data['store_view']];
                } elseif (is_array($data['store_view'])) {
                    $data['store_view'] = array_values(array_map('intval', $data['store_view']));
                } else {
                    $data['store_view'] = [];
                }
            } else {
                $data['store_view'] = [];
            }

            if (isset($data['customer_group']) && $data['customer_group'] !== null && $data['customer_group'] !== '') {
                if (is_string($data['customer_group'])) {
                    $parts = array_filter(array_map('trim', explode(',', $data['customer_group'])), function ($v) {
                        return $v !== '';
                    });
                    $data['customer_group'] = array_values(array_map('intval', $parts));
                } elseif (is_numeric($data['customer_group'])) {
                    $data['customer_group'] = [(int)$data['customer_group']];
                } elseif (is_array($data['customer_group'])) {
                    $data['customer_group'] = array_values(array_map('intval', $data['customer_group']));
                } else {
                    $data['customer_group'] = [];
                }
            } else {
                $data['customer_group'] = [];
            }

            $this->loadedData[$faq->getId()] = $data;
        }

        return $this->loadedData;
    }
}