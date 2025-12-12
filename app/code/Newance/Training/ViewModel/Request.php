<?php

namespace Newance\Training\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Cms\Model\PageRepository;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Request
 * @package Newance\Training\ViewModel
 */
class Request implements ArgumentInterface
{
    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Request constructor.
     * @param PageRepository $pageRepository
     * @param FilterProvider $filterProvider
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        PageRepository $pageRepository,
        FilterProvider $filterProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->pageRepository = $pageRepository;
        $this->filterProvider = $filterProvider;
        $this->storeManager = $storeManager;
    }

    /**
     * Get filtered page content by page identifier
     *
     * @param int|string $pageIdentifier The page identifier
     * @return string|null Error message or filtered content
     */
    public function getPageContent($pageIdentifier)
    {
        $storeId = $this->storeManager->getStore()->getId();

        try {
            $page = $this->pageRepository->getById($pageIdentifier, $storeId);
            $pageContent = $page->getContent();
            $filteredContent = $this->filterProvider->getPageFilter()->filter($pageContent);

            return $filteredContent;
        } catch (\Exception $e) {
            return "Error loading CMS page content: " . $e->getMessage();
        }
    }
}
