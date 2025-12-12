<?php

namespace Iflair\Delivery\Model;

use Magento\Framework\Model\AbstractModel;

class Delivery extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Iflair\Delivery\Model\ResourceModel\Delivery::class);
    }
}