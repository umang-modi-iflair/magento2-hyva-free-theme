<?php

namespace Iflair\CustomerBought\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomerBought extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('iflair_bundle_packs', 'pack_id');
    }
}
