<?php

namespace Iflair\Delivery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Delivery extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('pincode_delivery_info', 'id');
    }
}
