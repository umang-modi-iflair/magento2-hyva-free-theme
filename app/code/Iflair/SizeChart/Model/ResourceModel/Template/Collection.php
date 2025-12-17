<?php
namespace Iflair\SizeChart\Model\ResourceModel\Template;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'template_id';
    protected function _construct()
    {
        $this->_init(
            \Iflair\SizeChart\Model\Template::class,
            \Iflair\SizeChart\Model\ResourceModel\Template::class
        );
    }
}
