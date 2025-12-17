<?php
namespace Iflair\SizeChart\Block\Adminhtml\SizeChart\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sizechart_tabs');
        $this->setDestElementId('add_form');
        $this->setTitle(__('Size Chart Template Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            [
                'label'   => __('General Information'),
                'title'   => __('General Information'),
                'content' => $this->getLayout()
                    ->createBlock(\Iflair\SizeChart\Block\Adminhtml\SizeChart\Edit\Tab\Main::class)
                    ->toHtml(),
                'active'  => true
            ]
        );

        return parent::_beforeToHtml();
    }
}
