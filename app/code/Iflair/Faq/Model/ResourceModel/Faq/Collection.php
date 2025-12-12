<?php

namespace Iflair\Faq\Model\ResourceModel\Faq;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Iflair\Faq\Model\Faq::class,
            \Iflair\Faq\Model\ResourceModel\Faq::class
        );
    }
}
