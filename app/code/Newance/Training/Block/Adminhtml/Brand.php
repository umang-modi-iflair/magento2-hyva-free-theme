<?php

namespace Newance\Training\Block\Adminhtml;

/**
 * Admin training brand
 */
class Brand extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Newance_Training';
        $this->_headerText = __('Brand');
        $this->_addButtonLabel = __('Add New Brand');
        parent::_construct();
    }
}
