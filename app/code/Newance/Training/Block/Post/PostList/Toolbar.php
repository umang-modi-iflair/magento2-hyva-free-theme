<?php

namespace Newance\Training\Block\Post\PostList;

/**
 * Training posts list toolbar
 */
class Toolbar extends \Magento\Framework\View\Element\Template
{
    /**
     * Page GET parameter name
     */
    const PAGE_PARM_NAME = 'page';

    /**
     * Products collection
     *
     * @var \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $_collection = null;

    /**
     * Default block template
     * @var string
     */
    protected $_template = 'post/list/toolbar.phtml';

    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }
        return $this;
    }

    /**
     * Return products collection instance
     *
     * @return \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get specified posts limit display per page
     *
     * @return string
     */
    public function getLimit()
    {
        if ($this->_request->getFullActionName() == 'cms_index_index') {
            $postLimit = $this->_scopeConfig->getValue(
                'newance_training/post_list/posts_per_page_home',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } else {
            $postLimit =  $this->_scopeConfig->getValue(
                'newance_training/post_list/posts_per_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $postLimit;
    }

    /**
     * Return current page from request
     *
     * @return int
     */
    public function getCurrentPage()
    {
        $page = (int) $this->_request->getParam(self::PAGE_PARM_NAME);
        return $page ? $page : 1;
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('post_list_toolbar_pager');
        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            /* @var $pagerBlock \Magento\Theme\Block\Html\Pager */

            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setPageVarName(
                'page'
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );
            return $pagerBlock->toHtml();
        }

        return '';
    }
}
