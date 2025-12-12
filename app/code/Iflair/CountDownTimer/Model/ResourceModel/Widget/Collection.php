<?php
namespace Iflair\CountDownTimer\Model\ResourceModel\Widget;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'widget_id';
    // protected $_eventPrefix = 'iflair_countdowntimer_widget_collection';
    // protected $_eventObject = 'widget_collection';

    protected function _construct()
    {
        $this->_init(
            \Iflair\CountDownTimer\Model\Widget::class,
            \Iflair\CountDownTimer\Model\ResourceModel\Widget::class
        );
    }
}
