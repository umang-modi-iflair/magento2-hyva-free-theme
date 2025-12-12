<?php

namespace Iflair\Faq\Model;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Area;

class SendMail
{
    protected $transportBuilder;
    protected $storeManager;

    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
    }

    public function sendFaqEmail($email, $customerName, $question, $answer)
    {
        // Safety check: Don't try to send if the recipient email is invalid or empty
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
             throw new \Magento\Framework\Exception\MailException(__('Invalid recipient email address provided.'));
        }

        $templateId = 'iflair_faq_email_template';

        $vars = [
            'customer_name' => $customerName,
            'faq_question' => $question,
            'faq_answer' => $answer,
            'this_year' => date('Y')
        ];

        // Define the sender details
        $from = [
            'name'  => 'Support Team',
            'email' => 'umang.modi@iflair.com'
        ];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions([
                // AREA_FRONTEND is correct if your template is defined in the frontend area
                'area' => Area::AREA_FRONTEND, 
                'store' => $this->storeManager->getStore()->getId()
            ])
            ->setTemplateVars($vars)
            ->setFrom($from) // <<-- FIX: Changed to setFrom() when using a custom name/email array
            ->addTo($email)
            ->getTransport();

        $transport->sendMessage();
    }
}