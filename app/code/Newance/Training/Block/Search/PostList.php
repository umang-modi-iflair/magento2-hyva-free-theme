<?php

namespace Newance\Training\Block\Search;

use Magento\Store\Model\ScopeInterface;

/**
 * Training search result block
 */
class PostList extends \Newance\Training\Block\Post\PostList
{
    /**
     * Retrieve query
     * @return string
     */
    public function getQuery()
    {
        return urldecode($this->getRequest()->getParam('q'));
    }

    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        parent::_preparePostCollection();

        $q = $this->getQuery();
        $this->_postCollection->addFieldToFilter(
            ['title', 'content_heading', 'content'],
            [
                ['like' => '%' . $q . '%'],
                ['like' => '%' . $q . '%'],
                ['like' => '% ' . $q . ' %']
            ]
        );
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $title = $this->_getTitle();
        $this->_addBreadcrumbs($title);
        $this->pageConfig->getTitle()->set($title);

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param  string $title
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs($title)
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
            $breadcrumbsBlock->addCrumb('training_search', ['label' => $title, 'title' => $title]);
        }
    }

    /**
     * Retrieve title
     * @return string
     */
    protected function _getTitle()
    {
        return __('Search') . ' "' . $this->getQuery() . '"';
    }
}
