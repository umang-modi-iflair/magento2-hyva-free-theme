<?php

namespace BluethinkInc\AddCustomTab\Model;

use Magento\Framework\Model\AbstractModel;

class CustomTab extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\BluethinkInc\AddCustomTab\Model\ResourceModel\CustomTab::class);
    }
}
