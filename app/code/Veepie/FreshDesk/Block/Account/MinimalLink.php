<?php
/**
 * Veepie FreshDesk MinimalLink Block
 *
 * @category   Veepie
 * @package    Veepie_FreshDesk
 * @author     Veepie Team
 */

namespace Veepie\FreshDesk\Block\Account;

use Magento\Customer\Block\Account\SortLink;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Request\Http;

/**
 * Class MinimalLink
 * @package Veepie\FreshDesk\Block\Account
 */
class MinimalLink extends SortLink
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var Http
     */
    private $request;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param CustomerSession $customerSession
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        CustomerSession $customerSession,
        Http $request,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->request = $request;

        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */

    public function toHtml()
    {
        if ($this->customerSession->getCustomer()->getEnableMyTicket()) {

            $fullActionName = $this->request->getFullActionName();
            
            $path = 'freshdesk/customer';
            
            if ($fullActionName == 'freshdesk_customer_index') {
                $path = 'freshdesk/customer';
            } elseif ($fullActionName == 'freshdesk_customer_create') {
                $path = 'freshdesk/customer/create';
            }
            
            $this->setData('path', $path);
            return parent::toHtml();
        }

        return '';
    }
}
