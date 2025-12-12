<?php

namespace Newance\Training\Block\Adminhtml\Post\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('General'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'related_posts_section',
            [
                'label' => __('Related Posts'),
                'url' => $this->getUrl('training/post/relatedPosts', ['_current' => true]),
                'class' => 'ajax',
            ]
        );

        $this->addTab(
            'related_products_section',
            [
                'label' => __('Related Products'),
                'url' => $this->getUrl('training/post/relatedProducts', ['_current' => true]),
                'class' => 'ajax',
            ]
        );
        return parent::_beforeToHtml();
    }
}
