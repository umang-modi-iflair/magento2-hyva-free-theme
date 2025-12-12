<?php
namespace Iflair\Faq\Block\Adminhtml\Faq\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Store\Model\System\Store;

class Main extends Generic implements TabInterface
{
    protected $_systemStore;
   protected $_visibilityOptions;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Iflair\Faq\Model\Source\VisibilityOptions $visibilityOptions,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_visibilityOptions = $visibilityOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('faq_data');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('faq_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model && $model->getId()) {
            $fieldset->addField('faq_id', 'hidden', [
                'name' => 'faq_id'
            ]);
        }

        $fieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Status'),
            'values' => [
                ['value' => 1, 'label' => __('Pending')],
                ['value' => 2, 'label' => __('Approved')],
            ]
        ]);

        $fieldset->addField('visibility', 'select', [
            'name'   => 'visibility',
            'label'  => __('Visibility'),
            'values' => $this->_visibilityOptions->toOptionArray()
        ]);


        $fieldset->addField('question', 'text', [
            'name' => 'question',
            'label' => __('Question'),
            'required' => true
        ]);

        $fieldset->addField('answer', 'textarea', [
            'name' => 'answer',
            'label' => __('Answer'),
            'required' => true
        ]);
     
        if ($model) {
            $form->setValues($model->getData());
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel() { return __('General'); }
    public function getTabTitle() { return __('General'); }
    public function canShowTab() { return true; }
    public function isHidden() { return false; }
}
