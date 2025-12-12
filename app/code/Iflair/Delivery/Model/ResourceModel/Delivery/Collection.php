<?php

namespace Iflair\Delivery\Model\ResourceModel\Delivery;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Iflair\Delivery\Model\Delivery::class,
            \Iflair\Delivery\Model\ResourceModel\Delivery::class
        );
    }
}
