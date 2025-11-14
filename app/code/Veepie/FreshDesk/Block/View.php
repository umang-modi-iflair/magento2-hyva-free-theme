<?php
/**
 * Veepie FreshDesk View Block
 *
 * @category   Veepie
 * @package    Veepie_FreshDesk
 * @author     Veepie Team
 */

namespace Veepie\FreshDesk\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use GuzzleHttp\ClientFactory;
use Veepie\FreshDesk\Block\Index;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class View
 * @package Veepie\FreshDesk\Block
 */
class View extends Template
{
    /**
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @var Index
     */
    protected $apiBlock;

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
     * View constructor.
     * @param Context $context
     * @param ClientFactory $clientFactory
     * @param Index $apiBlock
     * @param ResponseFactory $responseFactory
     * @param ManagerInterface $messageManager
     * @param UrlInterface $url
     * @param array $data
     */
    public function __construct(
        Context $context,
        ClientFactory $clientFactory,
        Index $apiBlock,
        ResponseFactory $responseFactory,
        ManagerInterface $messageManager,
        UrlInterface $url,
        array $data = []
    ) {
        $this->clientFactory = $clientFactory;
        $this->apiBlock = $apiBlock;
        $this->responseFactory = $responseFactory;
        $this->messageManager = $messageManager;
        $this->url = $url;
        parent::__construct($context, $data);
    }

    /**
     * Get ticket details from FreshDesk API
     *
     * @param int $ticketId
     * @return array
     */
    public function getTicketDetails($ticketId)
    {
        // Get API key and Freshdesk domain from Index block
        $apiKey = $this->apiBlock->getApiValue();
        $domain = $this->apiBlock->getDomainValue();
        $url = "https://$domain.freshdesk.com/api/v2/tickets/$ticketId";

        // Send GET request to Freshdesk API
        $client = $this->clientFactory->create();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode("$apiKey:x")
            ]
        ]);

        // Check if response is successful
        if ($response->getStatusCode() === 200) {
            $ticketDetails = json_decode($response->getBody(), true);
            // Check if the ticket is deleted
            if (isset($ticketDetails['deleted']) && $ticketDetails['deleted']) {
                //throw new \Exception('This ticket has been deleted.');
                $this->messageManager->addErrorMessage(__('This ticket has been deleted.'));
                $redirectUrl = $this->url->getUrl('freshdesk/customer');
                $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
                exit;
            }
            // Fetch user details using requester_id
            if (isset($ticketDetails['requester_id'])) {
                $userId = $ticketDetails['requester_id'];
                $userUrl = "https://$domain.freshdesk.com/api/v2/contacts/$userId";
                $userResponse = $client->request('GET', $userUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Basic ' . base64_encode("$apiKey:x")
                    ]
                ]);

                if ($userResponse->getStatusCode() === 200) {
                    $userDetails = json_decode($userResponse->getBody(), true);
                    $ticketDetails['requester_name'] = $userDetails['name'] ?? 'Unknown User';
                } else {
                    $ticketDetails['requester_name'] = 'Unknown User';
                }
            }
            return $ticketDetails;
        } else {
            return [];
        }
    }
    
    /**
     * Get the subject of the ticket
     *
     * @param array $ticketDetails
     * @return string
     */
    public function getTicketSubject($ticketDetails)
    {
        return $ticketDetails['subject'];
    }

    /**
     * Get the description of the ticket
     *
     * @param array $ticketDetails
     * @return string
     */
    public function getTicketDescription($ticketDetails)
    {
        return $ticketDetails['description'];
    }

    /**
     * Get the date of the ticket
     *
     * @param array $ticketDetails
     * @return string
     */
    public function getTicketCreatedAt($ticketDetails)
    {
        return $this->formatDateTime($ticketDetails['created_at']);
    }

    /**
     * Get the attachments of the ticket
     *
     * @param array $ticketDetails
     * @return array
     */
    public function getTicketAttachments($ticketDetails)
    {
        return $ticketDetails['attachments'];
    }

    /**
     * Get the ticket status label of the ticket
     *
     * @param array $ticketDetails
     * @return string
     */
    public function getTicketStatus($ticketDetails)
    {
        $statusMapping = $this->getStatusMapping();
        $statusCode = $ticketDetails['status'];

        return isset($statusMapping[$statusCode]) ? $statusMapping[$statusCode] : 'Unknown';
    }

    /**
     * Get the mapping of status codes to status labels
     *
     * @return array The mapping of status codes to labels.
     */
    public function getStatusMapping()
    {
        return [
            2 => __('Open'),
            3 => __('Pending'),
            4 => __('Resolved'),
            5 => __('Closed'),
        ];
    }

    /**
     * Convert bytes to kilobytes
     *
     * @param int $bytes The size in bytes.
     * @return string The size in kilobytes, formatted to 2 decimal places.
     */
    public function getBytesToKilobytes($bytes)
    {
        return number_format($bytes / 1000, 2) . ' KB';
    }

    /**
     * Get the file extension from a filename
     *
     * @param string $filename The name of the file.
     * @return string The file extension.
     */
    public function getFileExtension($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    /**
     * Get ticket reply details from FreshDesk API
     *
     * @param int $ticketId
     * @return array
     */
    public function getTicketReplyDetails($ticketId)
    {
        // Get API key and Freshdesk domain from Index block
        $apiKey = $this->apiBlock->getApiValue();
        $domain = $this->apiBlock->getDomainValue();
        $url = "https://$domain.freshdesk.com/api/v2/tickets/$ticketId/conversations";

        // Send GET request to Freshdesk API
        $client = $this->clientFactory->create();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode("$apiKey:x")
            ]
        ]);

        $data = [];

        // Check if response is successful
        if ($response->getStatusCode() === 200) {
            $replyDetails = json_decode($response->getBody(), true);

            // Iterate through each reply to extract details and attachments
            foreach ($replyDetails as $reply) {
                if (isset($reply['source']) && $reply['source'] == 0) {
                    $replyData = [
                        'id' => $reply['id'],
                        'body' => $reply['body'],
                        'created_at' => $this->formatDateTime($reply['created_at']),
                        'updated_at' => $this->formatDateTime($reply['updated_at']),
                        'attachments' => []
                    ];

                    // Check if there are attachments and add details to reply data
                    if (isset($reply['attachments']) && is_array($reply['attachments'])) {
                        foreach ($reply['attachments'] as $attachment) {
                            $replyData['attachments'][] = [
                                'id' => $attachment['id'],
                                'name' => $attachment['name'],
                                'size' => $attachment['size'],
                                'url' => $attachment['attachment_url'],
                                'type' => $this->getFileExtension($attachment['name']),
                                'content_type' => $attachment['content_type']
                            ];
                        }
                    }
                    $data[] = $replyData;
                }
            }
            return $data;
        } else {
            return [];
        }
    }

    /**
     * Format the given date and time
     *
     * @param string $dateTime The date and time string to format.
     * @param string $format The desired format for the date and time. Default is 'd/m/Y H:i'.
     * @return string The formatted date and time.
     */
    public function formatDateTime($dateTime, $format = 'd/m/Y H:i')
    {
        $date = new \DateTime($dateTime);
        return $date->format($format);
    }
}
