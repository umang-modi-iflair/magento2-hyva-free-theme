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
 * Class Reply
 *
 * @category   Veepie
 * @package    FreshDesk
 * @subpackage Controller\Customer
 */
class Reply extends Action
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
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $ticketId = $this->getRequest()->getPost('ticket_id');

        $replyMessage = $this->getRequest()->getPost('reply_message');

        $ticketStatusCode = $this->getRequest()->getPost('ticket_status');

        $apiKey = $this->apiBlock->getApiValue();
        $domain = $this->apiBlock->getDomainValue();

        try {
            // Reopen the ticket if it's closed
            if ($ticketStatusCode == 5) {
                $url = "https://$domain.freshdesk.com/api/v2/tickets/$ticketId";
                $response = $this->httpClient->put($url, [
                    'auth' => [$apiKey, 'X'], // Basic authentication
                    'json' => ['status' => 2] // 2 is the status code for Open
                ]);

                if ($response->getStatusCode() !== 200) {
                    throw new LocalizedException(__('Failed to reopen the ticket. Please try again.'));
                }
            }

            $url = "https://$domain.freshdesk.com/api/v2/tickets/$ticketId/reply";

            // Prepare request body
            $data = [
                //VERIFY THIS
                // 'email' => $this->customerSession->getCustomer()->getEmail(),
                'body' => $replyMessage
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

            $multipart = [];
            foreach ($data as $name => $contents) {
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

            if ($response->getStatusCode() === 201) {
                // Redirect back to the ticket view page after submitting the reply
                $this->_redirect('freshdesk/customer/view', ['id' => $ticketId]);
                $this->messageManager->addSuccessMessage(__('Your reply has been sent successfully.'));
            } else {
                throw new LocalizedException(__('Failed to reply ticket. Please try again.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Your reply could not be sent. Error occured. (%1)', $e->getMessage()));
        }

        // Create a redirect result object to redirect back to the same page
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl()); // Redirect to the referring page
        return $resultRedirect; // Return the redirect result
    }
}
