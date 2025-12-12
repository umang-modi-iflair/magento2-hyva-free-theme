<?php

namespace Newance\Training\Block\Brand;

use Magento\Store\Model\ScopeInterface;

/**
 * Training brand view
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
        if ($brand = $this->getBrand()) {
            $brands = $brand->getChildrenIds();
            $brands[] = $brand->getId();
            $this->_postCollection->addBrandFilter($brands);
        }
    }

    /**
     * Retrieve brand instance
     *
     * @return \Newance\Training\Model\Brand
     */
    public function getBrand()
    {
        return $this->_coreRegistry->registry('current_training_brand');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $brand = $this->getBrand();
        if ($brand) {
            $this->_addBreadcrumbs($brand);
            $this->pageConfig->addBodyClass('training-brand-' . $brand->getIdentifier());
            $this->pageConfig->getTitle()->set($this->_getTitle());
            $this->pageConfig->setKeywords($brand->getMetaKeywords());
            $this->pageConfig->setDescription($brand->getMetaDescription());
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Newance\Training\Model\Brand $brand
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs($brand)
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

            $_brand = $brand;
            $parentBrands = [];
            while ($parentBrand = $_brand->getParentBrand()) {
                $parentBrands[] = $_brand = $parentBrand;
            }

            for ($i = count($parentBrands) - 1; $i >= 0; $i--) {
                $_brand = $parentBrands[$i];
                $breadcrumbsBlock->addCrumb('training_parent_brand_' . $_brand->getId(), [
                    'label' => $_brand->getTitle(),
                    'title' => $_brand->getTitle(),
                    'link'  => $_brand->getBrandUrl()
                ]);
            }

            $breadcrumbsBlock->addCrumb('training_brand', [
                'label' => $brand->getTitle(),
                'title' => $brand->getTitle()
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
        return $this->getBrand()->getTitle();
    }
}
