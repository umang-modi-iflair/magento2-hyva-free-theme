<?php

namespace Newance\Training\Block\Adminhtml\Brand\Edit\Tab;

/**
 * Admin training brand edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Newance\Training\Model\Config\Source\Brand
     */
    protected $_brandOption;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Newance\Training\Model\Config\Source\brand $brandOption
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Newance\Training\Model\Config\Source\Brand $brandOption,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_brandOption = $brandOption;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Newance\Training\Model\Brand */
        $model = $this->_coreRegistry->registry('current_model');

        /*
         * Checking if user have permissions to save information
         */
        $isElementDisabled = !$this->_isAllowedAction('Newance_Training::brand');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('brand_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Brand Information')]);

        if ($model->getId()) {
            $fieldset->addField('brand_id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Brand Title'),
                'title' => __('Brand Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'class' => 'validate-identifier',
                'note' => __('Relative to Web Site Base URL'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Brand Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_ids',
                'multiselect',
                [
                    'name' => 'store_ids[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_ids',
                'hidden',
                ['name' => 'store_ids[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreIds([$this->_storeManager->getStore(true)->getId()]);
        }

        $field = $fieldset->addField(
            'path',
            'select',
            [
                'name' => 'path',
                'label' => __('Parent Brand'),
                'title' => __('Parent Brand'),
                'values' => $this->_brandOption->toOptionArray(),
                'disabled' => $isElementDisabled,
                'style' => 'width:100%',
            ]
        );

        $fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'disabled' => $isElementDisabled
            ]
        );

        if (is_array($model->getData('banner_img'))) {
            $model->setData('banner_img', $model->getData('banner_img')['value']);
        }
        $fieldset->addField(
            'banner_img',
            'image',
            [
                'title' => __('Brand Banner (Overview)'),
                'label' => __('Brand Banner (Overview)'),
                'name' => 'brand[banner_img]',
                'note' => __('Allow image type: jpg, jpeg, gif, png'),
            ]
        );

        if (is_array($model->getData('brand_img'))) {
            $model->setData('brand_img', $model->getData('brand_img')['value']);
        }
        $fieldset->addField(
            'brand_img',
            'image',
            [
                'title' => __('Brand Logo (Detail Training)'),
                'label' => __('Brand Logo (Detail Training)'),
                'name' => 'brand[brand_img]',
                'note' => __('Allow image type: jpg, jpeg, gif, png'),
            ]
        );

        $this->_eventManager->dispatch('newance_training_brand_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Brand Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Brand Information');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
