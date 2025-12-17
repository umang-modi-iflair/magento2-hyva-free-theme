<?php
namespace Iflair\SizeChart\Model;

use Magento\Framework\Model\AbstractModel;

class Template extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Iflair\SizeChart\Model\ResourceModel\Template::class);
    }
}
