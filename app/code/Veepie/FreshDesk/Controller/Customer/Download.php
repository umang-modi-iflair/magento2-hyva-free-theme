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
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Download
 *
 * @category   Veepie
 * @package    FreshDesk
 * @subpackage Controller\Customer
 */

class Download extends Action
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $fileUrl = $this->getRequest()->getParam('file');
        $fileName = $this->getRequest()->getParam('name');

        if ($fileUrl && $fileName) {
            // Decode the URL
            $fileUrl = urldecode($fileUrl);
            $fileName = urldecode($fileName);

            // Get the file content
            $fileContent = @file_get_contents($fileUrl);
            if ($fileContent === FALSE) {
                $this->messageManager->addError(__('Failed to retrieve the file content.'));
                return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
            }

            // Set the appropriate headers to force download
            $this->getResponse()->clearHeaders();
            $this->getResponse()->setHttpResponseCode(200);
            $this->getResponse()->setHeader('Content-Description', 'File Transfer', true);
            $this->getResponse()->setHeader('Content-Type', 'application/octet-stream', true);
            $this->getResponse()->setHeader('Content-Disposition', 'attachment; filename="' . basename($fileName) . '"', true);
            $this->getResponse()->setHeader('Content-Transfer-Encoding', 'binary', true);
            $this->getResponse()->setHeader('Expires', '0', true);
            $this->getResponse()->setHeader('Cache-Control', 'must-revalidate', true);
            $this->getResponse()->setHeader('Pragma', 'public', true);
            $this->getResponse()->setHeader('Content-Length', strlen($fileContent), true);

            // Output the file content
            $this->getResponse()->setBody($fileContent);
            return;
        }

        $this->messageManager->addError(__('Invalid parameters.'));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
