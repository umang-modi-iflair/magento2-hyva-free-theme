<?php

namespace BluethinkInc\AddCustomTab\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomTab extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('bluethink_view', 'bluethink_id');
    }
}
