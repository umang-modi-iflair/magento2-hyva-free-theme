<?php
namespace Iflair\BarCode\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Iflair\BarCode\Helper\QrGenerator;
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
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );

        $mediaDir = $this->filesystem
            ->getDirectoryWrite(DirectoryList::MEDIA);

        $path = 'product_qr/';
        $mediaDir->create($path);

        $count = 0;

      foreach ($collection as $product) {

        $qrImage = $this->qrGenerator->generate(
            $product->getProductUrl()
        );

        $fileName = $path . $product->getSku() . '.png';
        $mediaDir->writeFile($fileName, $qrImage);

        $count++;
    }

        $this->messageManager->addSuccessMessage(
            __('QR Codes generated for %1 product(s).', $count)
        );

        return $this->_redirect('catalog/product/index');
    }
}
