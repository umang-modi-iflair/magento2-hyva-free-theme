<?php

namespace Newance\Training\Block\Adminhtml\Category\Edit;

/**
 * Admin training category edit form tabs
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('category_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Category Information'));
    }
}
