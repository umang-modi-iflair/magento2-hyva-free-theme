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
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Save
 *
 * @category   Veepie
 * @package    FreshDesk
 * @subpackage Controller\Customer
 */
class Save extends Action
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
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Constructor
     *
     * @param Context       $context       Context
     * @param ClientFactory $httpClientFactory Client factory
     * @param ResultFactory $resultFactory Result factory
     * @param Index $apiBlock Index Block
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        ClientFactory $httpClientFactory,
        ResultFactory $resultFactory,
        Index $apiBlock,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->httpClient = $httpClientFactory->create();
        $this->resultFactory = $resultFactory;
        $this->apiBlock = $apiBlock;
        $this->customerSession = $customerSession;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $apiKey = $this->apiBlock->getApiValue();
        $domain = $this->apiBlock->getDomainValue();
        $url = "https://$domain.freshdesk.com/api/v2/tickets";

        $ticketData = [
            'email' => $this->customerSession->getCustomer()->getEmail(),
            'subject' => $this->getRequest()->getParam('subject'),
            'type' => $this->getRequest()->getParam('type'),
            'status' => 2,
            'priority' => 2,
            'description' => $this->getRequest()->getParam('description')
        ];

        $attachments = [];
        if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
            foreach ($_FILES['attachments']['tmp_name'] as $index => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $attachments[] = [
                        'name' => $_FILES['attachments']['name'][$index],
                        'contents' => fopen($tmpName, 'r'),
                    ];
                }
            }
        }

        try {
            $multipart = [];
            foreach ($ticketData as $name => $contents) {
                $multipart[] = [
                    'name' => $name,
                    'contents' => $contents,
                ];
            }

            foreach ($attachments as $attachment) {
                $multipart[] = [
                    'name' => 'attachments[]',
                    'contents' => $attachment['contents'],
                    'filename' => $attachment['name']
                ];
            }

            $response = $this->httpClient->post($url, [
                'auth' => [$apiKey, 'X'], // Basic authentication
                'multipart' => $multipart,
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);

            if ($statusCode === 201) {
                $ticketId = $responseData['id'];
                $this->messageManager->addSuccessMessage(__('Ticket has been created successfully.'));
            } else {
                throw new LocalizedException(__('Failed to create ticket. Please try again.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Ticket has not been created. Error occurred. (%1)', $e->getMessage()));
        }

        // Create a redirect result object to redirect back to the same page
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl()); // Redirect to the referring page
        return $resultRedirect; // Return the redirect result
    }
}
