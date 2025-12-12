<?php

namespace Newance\Training\Block\Adminhtml;

/**
 * Admin training post
 */
class Post extends \Magento\Backend\Block\Widget\Grid\Container
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
        $this->_headerText = __('Post');
        $this->_addButtonLabel = __('Add New Post');
        parent::_construct();
    }
}
