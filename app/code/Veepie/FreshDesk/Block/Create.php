<?php
/**
 * Veepie FreshDesk Create Block
 *
 * @category   Veepie
 * @package    Veepie_FreshDesk
 * @author     Veepie Team
 */

namespace Veepie\FreshDesk\Block;

use Veepie\FreshDesk\Block\Index;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class Create
 * @package Veepie\FreshDesk\Block
 */
class Create extends \Magento\Framework\View\Element\Template
{

    /**
     * @var Index
     */
    protected $freshdeskCompany;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Index $freshdeskCompany
     * @param ResponseFactory $responseFactory
     * @param ManagerInterface $messageManager
     * @param UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Index $freshdeskCompany,
        ResponseFactory $responseFactory,
        ManagerInterface $messageManager,
        UrlInterface $url
    ) {
        $this->freshdeskCompany = $freshdeskCompany;
        $this->responseFactory = $responseFactory;
        $this->messageManager = $messageManager;
        $this->url = $url;
        parent::__construct($context);
    }

    /**
     * Prepare layout
     *
     * Set page title
     *
     * @return \Magento\Framework\View\Element\Template
     */
    public function _prepareLayout()
    {
        $freshdeskCompany = $this->freshdeskCompany->getFreshdeskCompanyId();

        if($freshdeskCompany) {
            $this->pageConfig->getTitle()->set(__('Create ticket'));
            return parent::_prepareLayout();    
        } else {
            $this->messageManager->addErrorMessage(__("New ticket can't create because of missing freshdesk Company ID."));
            $redirectUrl = $this->url->getUrl('freshdesk/customer');
            $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
            exit;
        }
    }
}
