<?php

namespace Iflair\CustomerBought\Model;

use Magento\Framework\Model\AbstractModel;

class CustomerBought extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Iflair\Faq\Model\ResourceModel\Faq::class);
    }
}