<?php
namespace Iflair\SizeChart\Model\ResourceModel\SizeChart;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
     protected $_idFieldName = 'sizeunit_id';
    protected function _construct()
    {
        $this->_init(
            \Iflair\SizeChart\Model\SizeChart::class,
            \Iflair\SizeChart\Model\ResourceModel\SizeChart::class
        );
    }
}
