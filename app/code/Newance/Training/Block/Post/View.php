<?php

namespace Newance\Training\Block\Post;

use Magento\Store\Model\ScopeInterface;

/**
 * Training post view
 */
class View extends AbstractPost
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $post = $this->getPost();
        if ($post) {
            $this->_addBreadcrumbs($post);
            $this->pageConfig->addBodyClass('training-post-' . $post->getIdentifier());
            $this->pageConfig->getTitle()->set($post->getTitle());
            $this->pageConfig->setKeywords($post->getMetaKeywords());
            $this->pageConfig->setDescription($post->getMetaDescription());
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Newance\Training\Model\Post $post
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs(\Newance\Training\Model\Post $post)
    {
        if (
            $this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE)
            && ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'training',
                [
                    'label' => __('Training'),
                    'title' => __('Training'),
                    'link' => $this->_url->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb('training_post', [
                'label' => $post->getTitle(),
                'title' => $post->getTitle()
            ]);
        }
    }
}
