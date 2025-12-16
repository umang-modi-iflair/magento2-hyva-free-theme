<?php
namespace Iflair\SizeChart\Model;

use Magento\Framework\Model\AbstractModel;

class SizeChart extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Iflair\SizeChart\Model\ResourceModel\SizeChart::class);
    }
}
