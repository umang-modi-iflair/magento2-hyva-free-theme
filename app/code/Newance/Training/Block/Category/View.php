<?php

namespace Newance\Training\Block\Category;

use Magento\Store\Model\ScopeInterface;

/**
 * Training category view
 */
class View extends \Newance\Training\Block\Post\PostList
{
    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        parent::_preparePostCollection();
        if ($category = $this->getCategory()) {
            $categories = $category->getChildrenIds();
            $categories[] = $category->getId();
            $this->_postCollection->addCategoryFilter($categories);
        }
    }

    /**
     * Retrieve category instance
     *
     * @return \Newance\Training\Model\Category
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_training_category');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $category = $this->getCategory();
        if ($category) {
            $this->_addBreadcrumbs($category);
            $this->pageConfig->addBodyClass('training-category-' . $category->getIdentifier());
            $this->pageConfig->getTitle()->set($this->_getTitle());
            $this->pageConfig->setKeywords($category->getMetaKeywords());
            $this->pageConfig->setDescription($category->getMetaDescription());
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Newance\Training\Model\Category $category
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs($category)
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

            $_category = $category;
            $parentCategories = [];
            while ($parentCategory = $_category->getParentCategory()) {
                $parentCategories[] = $_category = $parentCategory;
            }

            for ($i = count($parentCategories) - 1; $i >= 0; $i--) {
                $_category = $parentCategories[$i];
                $breadcrumbsBlock->addCrumb('training_parent_category_' . $_category->getId(), [
                    'label' => $_category->getTitle(),
                    'title' => $_category->getTitle(),
                    'link'  => $_category->getCategoryUrl()
                ]);
            }

            $breadcrumbsBlock->addCrumb('training_category', [
                'label' => $category->getTitle(),
                'title' => $category->getTitle()
            ]);
        }
    }

    /**
     * Retrieve title
     *
     * @return string
     */
    protected function _getTitle()
    {
        $category = $this->getCategory();
        return sprintf(__('Category: %s'), $category->getTitle());
    }
}
