<?php
namespace Iflair\SizeChart\Block\Adminhtml\SizeChart;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'template_id';
        $this->_blockGroup = 'Iflair_SizeChart';
        $this->_controller = 'adminhtml_sizechart';
        
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Template'));
        $this->buttonList->update('delete', 'label', __('Delete Template'));
        
        $this->_formBlockName = \Iflair\SizeChart\Block\Adminhtml\SizeChart\Edit\Form::class;
    }
}