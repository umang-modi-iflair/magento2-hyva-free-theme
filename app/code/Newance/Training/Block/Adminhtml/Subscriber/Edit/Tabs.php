<?php

namespace Newance\Training\Block\Adminhtml\Subscriber\Edit;

/**
 * Admin training subscriber edit form tabs
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('subscriber_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Subscriber'));
    }
}
