<?php
namespace Iflair\SizeChart\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Template extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('size_chart_templates', 'template_id');
    }
}
