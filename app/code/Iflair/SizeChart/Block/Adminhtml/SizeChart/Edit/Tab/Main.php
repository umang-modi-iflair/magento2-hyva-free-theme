<?php
namespace Iflair\SizeChart\Block\Adminhtml\SizeChart\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Store\Model\System\Store;

class Main extends Generic implements TabInterface
{
    /**
     * @var Store
     */
    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     */
    protected function _prepareForm()
    {
        /** @var \flair\SizeChart\Model\Template $model */
        $model = $this->_coreRegistry->registry('sizechart_data');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('template_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class'  => 'fieldset-wide'
            ]
        );

        // Hidden ID field (Edit mode)
        if ($model && $model->getId()) {
            $fieldset->addField(
                'template_id',
                'hidden',
                [
                    'name' => 'template_id'
                ]
            );
        }

        $fieldset->addField(
            'template_name',
            'text',
            [
                'name'     => 'template_name',
                'label'    => __('Template Name'),
                'title'    => __('Template Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'template_code',
            'text',
            [
                'name'     => 'template_code',
                'label'    => __('Template Code'),
                'title'    => __('Template Code'),
                'required' => true,
                'note'     => __('Unique code used to identify the template')
            ]
        );

        $fieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Status'),
            'values' => [
                ['value' => 0, 'label' => __('Enabled')],
                ['value' => 1, 'label' => __('Disabled')],
            ]
        ]);

        // Set existing data on edit
        if ($model) {
            $form->setValues($model->getData());
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /** Tab label */
    public function getTabLabel()
    {
        return __('General');
    }

    /** Tab title */
    public function getTabTitle()
    {
        return __('General');
    }

    /** Show tab */
    public function canShowTab()
    {
        return true;
    }

    /** Hide tab */
    public function isHidden()
    {
        return false;
    }
}
