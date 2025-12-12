<?php

namespace Newance\Training\Controller\Request;

use Magento\Framework\App\Action\Context;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class Index
 * @package Newance\Training\Controller\Request
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Constructor
     *
     * @param Context $context
     * @param RequestInterface $request
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->pageFactory = $pageFactory;
    }

    /**
     * Executes the action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Opleiding op maat'));
        $this->getBreadcrumbs();

        return $resultPage;
    }

    /**
     * Get breadcrumbs for the page
     *
     * @return \Magento\Framework\View\Element\AbstractBlock|null
     */
    public function getBreadcrumbs()
    {
        $resultPage = $this->pageFactory->create();
        $breadcrumbsBlock = $resultPage->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label'    => __('Home'),
                    'link'     => $this->_url->getUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'training',
                [
                    'label'    => __('Trainings'),
                    'link'     => $this->_url->getUrl('trainings')
                ]
            );
            /* you can add here your condition for dynamic element */
            $breadcrumbsBlock->addCrumb(
                'request',
                [
                    'label'    => __('Opleiding op maat')
                ]
            );
        }
        return $breadcrumbsBlock;
    }
}
