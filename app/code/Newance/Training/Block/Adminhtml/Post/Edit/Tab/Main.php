<?php

namespace Newance\Training\Block\Adminhtml\Post\Edit\Tab;

/**
 * Admin training post edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Newance\Training\Model\Config\Source\Category
     */
    protected $_categoryOption;

    /**
     * @var \Newance\Training\Model\Config\Source\Brand
     */
    protected $_brandOption;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Newance\Training\Model\Config\Source\Category $categoryOption
     * @param \Newance\Training\Model\Config\Source\Brand $brandOption
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Newance\Training\Model\Config\Source\Category $categoryOption,
        \Newance\Training\Model\Config\Source\Brand $brandOption,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_categoryOption = $categoryOption;
        $this->_brandOption = $brandOption;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('current_model');

        /*
         * Checking if user have permissions to save information
         */
        $isElementDisabled = !$this->_isAllowedAction('Newance_Training::post');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('post_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('post_id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'post[title]',
                'label' => __('Post Title'),
                'title' => __('Post Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'post[identifier]',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'class' => 'validate-identifier',
                'note' => __('Relative to Web Site Base URL'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'location',
            'text',
            [
                'name' => 'post[location]',
                'label' => __('Location'),
                'title' => __('Location'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'hour',
            'text',
            [
                'name' => 'post[hour]',
                'label' => __('Hour'),
                'title' => __('Hour'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'amount',
            'text',
            [
                'name' => 'post[amount]',
                'label' => __('Amount'),
                'title' => __('Amount'),
                'note' => __('Do not add Euro sign (ex. 100)'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'remaining_places',
            'text',
            [
                'name' => 'post[remaining_places]',
                'label' => __('Remaining places'),
                'title' => __('Remaining places'),
                'note' => __('Number (ex. 10)'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Post Status'),
                'name' => 'post[is_active]',
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
                    'name' => 'post[store_ids][]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled,
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
                ['name' => 'post[store_ids][]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreIds([$this->_storeManager->getStore(true)->getId()]);
        }

        if (count($this->_categoryOption->toOptionArray()) > 1) {
            $field = $fieldset->addField(
                'categories',
                'multiselect',
                [
                    'name' => 'post[categories][]',
                    'label' => __('Categories'),
                    'title' => __('Categories'),
                    'values' => $this->_categoryOption->toOptionArray(),
                    'disabled' => $isElementDisabled,
                    'style' => 'width:100%',
                ]
            );
        }

        if (count($this->_brandOption->toOptionArray()) > 1) {
            $field = $fieldset->addField(
                'brands',
                'multiselect',
                [
                    'name' => 'post[brands][]',
                    'label' => __('Brands'),
                    'title' => __('Brands'),
                    'values' => $this->_brandOption->toOptionArray(),
                    'disabled' => $isElementDisabled,
                    'style' => 'width:100%',
                ]
            );
        }

        if (is_array($model->getData('home_img'))) {
            $model->setData('home_img', $model->getData('home_img')['value']);
        }
        $fieldset->addField(
            'home_img',
            'image',
            [
                'title' => __('Image (Homepage)'),
                'label' => __('Image (Homepage)'),
                'name' => 'post[home_img]',
                'note' => __('Allow image type: jpg, jpeg, gif, png'),
            ]
        );

        if (is_array($model->getData('featured_img'))) {
            $model->setData('featured_img', $model->getData('featured_img')['value']);
        }
        $fieldset->addField(
            'featured_img',
            'image',
            [
                'title' => __('Banner Image (Detail)'),
                'label' => __('Banner Image (Detail)'),
                'name' => 'post[featured_img]',
                'note' => __('Allow image type: jpg, jpeg, gif, png'),
            ]
        );

        $fieldset->addField(
            'subscription_form',
            'select',
            [
                'label' => __('Show subscription form'),
                'title' => __('Show subscription form'),
                'name' => 'post[subscription_form]',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setData('subscription_form', $isElementDisabled ? '0' : '1');
        }

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );

        $fieldset->addField(
            'publish_time',
            'date',
            [
                'name' => 'post[publish_time]',
                'label' => __('Date training'),
                'date_format' => $dateFormat,
                'disabled' => $isElementDisabled,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );

        $this->_eventManager->dispatch('newance_training_post_edit_tab_main_prepare_form', ['form' => $form]);

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
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General');
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
