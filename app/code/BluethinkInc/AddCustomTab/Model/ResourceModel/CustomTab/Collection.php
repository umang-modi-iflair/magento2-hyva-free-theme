<?php

namespace BluethinkInc\AddCustomTab\Model\ResourceModel\CustomTab;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \BluethinkInc\AddCustomTab\Model\CustomTab::class,
            \BluethinkInc\AddCustomTab\Model\ResourceModel\CustomTab::class
        );
    }
}
