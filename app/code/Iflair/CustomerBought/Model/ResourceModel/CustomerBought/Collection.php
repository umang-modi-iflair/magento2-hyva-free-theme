<?php

namespace Iflair\CustomerBought\Model\ResourceModel\CustomerBought;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Iflair\CustomerBought\Model\CustomerBought::class,
            \Iflair\CustomerBought\Model\ResourceModel\CustomerBought::class
        );
    }
}
