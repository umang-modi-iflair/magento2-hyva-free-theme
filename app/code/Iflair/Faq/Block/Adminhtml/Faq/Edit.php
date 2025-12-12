<?php
namespace Iflair\Faq\Block\Adminhtml\Faq;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'faq_id';
        $this->_blockGroup = 'Iflair_Faq';
        $this->_controller = 'adminhtml_faq';
        
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save FAQ'));
        $this->buttonList->update('delete', 'label', __('Delete FAQ'));

        // $this->buttonList->add(
        //     'saveandcontinue',
        //     [
        //         'label' => __('Save and Continue Edit'),
        //         'class' => 'save',
        //         'data_attribute' => [
        //             'mage-init' => [
        //                 'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
        //             ],
        //         ]
        //     ],
        //     -100
        // );
        
        $this->_formBlockName = \Iflair\Faq\Block\Adminhtml\Faq\Edit\Form::class;
    }
}