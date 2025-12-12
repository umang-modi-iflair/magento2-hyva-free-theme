<?php

namespace Newance\Training\Block\Post\PostList\Toolbar;

/**
 * Toolbar pager
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * Retrieve url of all pages
     *
     * @return string
     */
    public function getPagesUrls()
    {
        $urls = [];
        for ($page = $this->getCurrentPage() + 1; $page <= $this->getLastPageNum(); $page++) {
            $urls[$page] = $this->getPageUrl($page);
        }

        return $urls;
    }
}
