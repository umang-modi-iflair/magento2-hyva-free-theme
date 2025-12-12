<?php
namespace Newance\Training\Controller\Adminhtml\Subscriber;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

class ExportCsv extends \Newance\Training\Controller\Adminhtml\Subscriber
{
    /**
     * Export subscribers grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = 'subscribers.csv';

        $content = $this->_view->getLayout()->getChildBlock('training.subscriber.grid', 'grid.export');

        return $this->_fileFactory->create(
            $fileName,
            $content->getCsvFile($fileName),
            DirectoryList::VAR_DIR
        );
    }
}
