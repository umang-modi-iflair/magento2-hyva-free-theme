<?php
namespace Iflair\SizeChart\Model\ResourceModel\Measurement;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'measurement_id';
    protected function _construct()
    {
        $this->_init(
            \Iflair\SizeChart\Model\Measurement::class,
            \Iflair\SizeChart\Model\ResourceModel\Measurement::class
        );
    }
}
