<?php
namespace Iflair\SizeChart\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Measurement extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('size_chart_measurement', 'measurement_id');
    }
}
