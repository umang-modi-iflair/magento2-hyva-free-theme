<?php

/**
 * Veepie FreshDesk Index Block
 *
 * @category   Veepie
 * @package    Veepie_FreshDesk
 * @author     Veepie Team
 */

namespace Veepie\FreshDesk\Block;

use Exception;
use GuzzleHttp\Client;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use GuzzleHttp\Exception\ClientException;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
// use Appseconnect\B2BMage\Helper\ContactPerson\Data as ContactPersonDataHelper;

/**
 * Class Index
 * @package Veepie\FreshDesk\Block
 */
class Index extends Template
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ContactPersonDataHelper
     */
  //  protected $contactPersonDataHelper;

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
     * Index constructor.
     * @param Template\Context $context
     * @param Client $httpClient
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $customerSession
   //  * @param ContactPersonDataHelper $contactPersonDataHelper
     * @param ResponseFactory $responseFactory
     * @param ManagerInterface $messageManager
     * @param UrlInterface $url
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Client $httpClient,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
     //   ContactPersonDataHelper $contactPersonDataHelper,
        ResponseFactory $responseFactory,
        ManagerInterface $messageManager,
        UrlInterface $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpClient = $httpClient;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
       // $this->contactPersonDataHelper = $contactPersonDataHelper;
        $this->responseFactory = $responseFactory;
        $this->messageManager = $messageManager;
        $this->url = $url;
    }

    /**
     * Get tickets associated with the customer's FreshDesk company ID
     *
     * @return array
     */
    public function getTickets()
    {
        $customerContactId = $this->customerSession->getCustomer()->getId();
        $parentCustomer = $this->contactPersonDataHelper->getParentCustomerDataByContactPersonId($customerContactId);
        $freshdeskCompanyId = null;

        $tickets = [];
        $data = [];

        if (is_array($parentCustomer)) {
            $parentCustomerId = $parentCustomer['customer_id'];
            $parentCustomerData = $this->contactPersonDataHelper->getCustomerData($parentCustomerId);
            if (isset($parentCustomerData['freshdesk_company_id'])) {
                $freshdeskCompanyId = $parentCustomerData['freshdesk_company_id'];
            }
        }

        if (empty($freshdeskCompanyId)) {
            return [];
        }

        // Get API key and Freshdesk domain from system configuration
        $apiKey = $this->getApiValue();
        $domain = $this->getDomainValue();
        $url = "https://$domain.freshdesk.com/api/v2/tickets?company_id=$freshdeskCompanyId";

        try {
            // Retrieve tickets from Freshdesk API
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($apiKey . ":X")
                ]
            ]);

            $tickets = json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            // Redirect to the customer account dashboard
            $this->messageManager->addErrorMessage(__('Invalid company ID provided. Please contact administrator.'));
            $redirectUrl = $this->url->getUrl('customer/account');
            $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
            exit;
        } catch (Exception $e) {
            // Handle other exceptions if necessary
            throw new LocalizedException(__('An error occurred while retrieving tickets.'));
        }

        //mapping of status values to status labels
        $statusMapping = [
            2 => $this->_escaper->escapeHtml(__('Open')),
            3 => $this->_escaper->escapeHtml(__('Pending')),
            4 => $this->_escaper->escapeHtml(__('Resolved')),
            5 => $this->_escaper->escapeHtml(__('Closed')),
        ];

        //iterate through retrieved tickets
        if (!empty($tickets)) {
            foreach ($tickets as $ticket) {
                $statusValue = $ticket['status'];

                //prepare ticket data for display
                $data[] = [
                    'id' => strval($ticket['id']),
                    'subject' => $ticket['subject'],
                    'created_at' => $this->dateformat($ticket['created_at']),
                    'updated_at' => $this->dateformat($ticket['updated_at']),
                    'status' => $statusMapping[$statusValue] ?? 'Unknown',
                    'url' => $this->getTicketUrl($ticket),
                ];
            }
        }

        return $data;
    }

    /**
     * Generate and return the URL to view a ticket
     *
     * @param array $ticket
     * @return string
     */
    public function getTicketUrl($ticket)
    {
        return $this->getUrl('freshdesk/customer/view', ['id' => $ticket['id']]);
    }

    /**
     * Format date string to the desired format
     *
     * @param string $dateString
     * @return string
     */
    public function dateformat($dateString)
    {
        return date('d/m/Y', strtotime($dateString));
    }

    /**
     * Generate and return "Create Ticket" URL
     *
     * @return string
     */
    public function getCreateUrl(): string
    {
        return $this->getUrl(
            'freshdesk/customer/create',
            ['_secure' => true]
        );
    }

    /**
     * Get customer's FreshDesk company ID
     *
     * @return string
     */
    public function getFreshdeskCompanyId()
    {
        $customerContactId = $this->customerSession->getCustomer()->getId();
        $parentCustomer = $this->contactPersonDataHelper->getParentCustomerDataByContactPersonId($customerContactId);
        $freshdeskCompanyId = null;

        if (is_array($parentCustomer)) {
            $parentCustomerId = $parentCustomer['customer_id'];
            $parentCustomerData = $this->contactPersonDataHelper->getCustomerData($parentCustomerId);
            if (isset($parentCustomerData['freshdesk_company_id'])) {
                $freshdeskCompanyId = $parentCustomerData['freshdesk_company_id'];
            }
        }
        return empty($freshdeskCompanyId) ? null : $freshdeskCompanyId;
    }

    /**
     * Get the API value from system configuration
     *
     * @return mixed
     */
    public function getApiValue()
    {
        return $this->scopeConfig->getValue(
            'veepie_freshdesk/freshdesk_api_configuration/freshdesk_api',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
    }

    /**
     * Get the domain value from system configuration
     *
     * @return mixed
     */
    public function getDomainValue()
    {
        return $this->scopeConfig->getValue(
            'veepie_freshdesk/freshdesk_api_configuration/freshdesk_domain',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
    }
}
