<?php

namespace Newance\Training\Block\Adminhtml;

/**
 * Admin training commment
 */
class Subscriber extends \Magento\Backend\Block\Widget\Grid\Container
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
        $this->_headerText = __('Subscriber');
        $this->_addButtonLabel = __('Add New Subscriber');
        parent::_construct();
    }
}
