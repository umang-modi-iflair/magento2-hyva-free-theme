<?php
namespace Iflair\QRCode\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Iflair\QRCode\Helper\QrGenerator;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class MassGenerate extends Action
{
    const ADMIN_RESOURCE = 'Magento_Catalog::products';

    protected $filter;
    protected $collectionFactory;
    protected $qrGenerator;
    protected $filesystem;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        QrGenerator $qrGenerator,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->qrGenerator = $qrGenerator;
        $this->filesystem = $filesystem;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $mediaWriter = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            
            $path = 'product_qr/';
            $mediaWriter->create($path);

            $count = 0;
            foreach ($collection as $product) {
                $qrData = $product->getProductUrl();
                $qrImage = $this->qrGenerator->generate($qrData);

                $safeSku = preg_replace('/[^A-Za-z0-9\-]/', '_', $product->getSku());
                $fileName = $path . $safeSku . '.png';

                $mediaWriter->writeFile($fileName, $qrImage);
                $count++;
            }

            $this->messageManager->addSuccessMessage(__('Successfully generated %1 QR codes.', $count));

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error generating QR codes: %1', $e->getMessage()));
        }

        return $this->_redirect('catalog/product/index');
    }
}