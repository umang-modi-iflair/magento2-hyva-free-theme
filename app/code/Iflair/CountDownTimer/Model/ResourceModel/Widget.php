<?php
namespace Iflair\CountDownTimer\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Widget extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('iflair_countdown_widget', 'widget_id');
    }
}
