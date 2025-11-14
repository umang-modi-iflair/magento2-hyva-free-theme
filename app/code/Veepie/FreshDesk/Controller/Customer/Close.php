<?php
/**
 *
 * @category   Veepie
 * @package    FreshDesk
 * @subpackage Controller\Customer
 * @author     Veepie Team
 */

namespace Veepie\FreshDesk\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use GuzzleHttp\ClientFactory;
use Magento\Framework\Controller\ResultFactory;
use Veepie\FreshDesk\Block\Index;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Close
 * 
 * Handles the closing of a Freshdesk ticket via a Magento controller action.
 *
 * @category   Veepie
 * @package    FreshDesk
 * @subpackage Controller\Customer
 */
class Close extends Action
{
    /**
     * @var ClientFactory
     */
    protected $httpClient;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Index
     */
    protected $apiBlock;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ClientFactory $httpClientFactory
     * @param ResultFactory $resultFactory
     * @param Index $apiBlock Index Block
     */
    public function __construct(
        Context $context,
        ClientFactory $httpClientFactory,
        ResultFactory $resultFactory,
        Index $apiBlock
    ) {
        parent::__construct($context);
        $this->httpClient = $httpClientFactory->create();
        $this->resultFactory = $resultFactory;
        $this->apiBlock = $apiBlock;
    }

    /**
     * Execute method
     *
     * Closes a Freshdesk ticket by updating its status to 'Closed' (status code 5).
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        // Get the ticket ID from the request parameters
        $ticketId = $this->getRequest()->getParam('id');
        
        // Get API key and Freshdesk domain from the Index block
        $apiKey = $this->apiBlock->getApiValue();
        $domain = $this->apiBlock->getDomainValue();
        $url = "https://$domain.freshdesk.com/api/v2/tickets/$ticketId";

        // Prepare request body with the status set to 'Closed'
        $data = [
            'status' => 5 // 5 is the status code for Closed
        ];

        try {
            // Send PUT request to Freshdesk API to close the ticket
            $response = $this->httpClient->put($url, [
                'auth' => [$apiKey, 'X'], // Basic authentication
                'json' => $data,
            ]);

            // Check if the response is successful
            if ($response->getStatusCode() === 200) {
                $this->messageManager->addSuccessMessage(__('The ticket has been closed successfully.'));
            } else {
                throw new LocalizedException(__('Failed to close the ticket. Please try again.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('The ticket could not be closed. Error occurred. (%1)', $e->getMessage()));
        }

        // Create a redirect result object to redirect back to the same page
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl()); // Redirect to the referring page
        return $resultRedirect; // Return the redirect result
    }
}
