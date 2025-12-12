<?php
namespace Iflair\CountDownTimer\Model;

use Magento\Framework\Model\AbstractModel;

class Widget extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Iflair\CountDownTimer\Model\ResourceModel\Widget::class);
    }

    public function beforeSave()
    {
        parent::beforeSave();

        // Convert multiselect array to comma separated string
        if (is_array($this->getData('display_on'))) {
            $this->setData('display_on', implode(',', $this->getData('display_on')));
        }

        if (is_array($this->getData('category_ids'))) {
            $this->setData('category_ids', implode(',', $this->getData('category_ids')));
        }

        return $this;
    }

}
