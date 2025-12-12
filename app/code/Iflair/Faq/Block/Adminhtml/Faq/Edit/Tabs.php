<?php

namespace Iflair\Faq\Block\Adminhtml\Faq\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('faq_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('FAQ Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            [
                'label'   => __('General Information'),
                'title'   => __('General Information'),
                'content' => $this->getLayout()
                    ->createBlock(\Iflair\Faq\Block\Adminhtml\Faq\Edit\Tab\Main::class)
                    ->toHtml(),
                'active'  => true
            ]
        );

        $this->addTab(
            'products_section',
            [
                'label'   => __('Products'),
                'title'   => __('Products'),
                'content' => $this->getLayout()
                    ->createBlock(\Iflair\Faq\Block\Adminhtml\Faq\Edit\Tab\Products::class)
                    ->toHtml()
            ]
        );

        return parent::_beforeToHtml();
    }
}
