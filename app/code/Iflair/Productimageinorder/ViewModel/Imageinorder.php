<?php

namespace Iflair\Productimageinorder\ViewModel;

use Iflair\Productimageinorder\Helper\Data as ProductImgHelper;
use Magento\Catalog\Helper\Image as HelperImage;

class Imageinorder implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const IMAGEWIDTH = 200;
    const IMAGEHEIGTH = 200;

    protected $helperImg;
    protected $productImgHelper;

    public function __construct(HelperImage $helperImg, ProductImgHelper $productImgHelper)
    {
        $this->helperImg = $helperImg;
        $this->productImgHelper = $productImgHelper;
    }

    public function isOn($order)
    {
        $storeId = $order->getStoreId();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $store = $objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStore($storeId);

        $websiteId = $store->getWebsiteId();
        return $this->productImgHelper->isEnabled($websiteId);
    }

    public function getImageUrl($product)
    {

        return $this->helperImg->init($product, 'small_image')->setImageFile($product->getSmallImage())->resize(self::IMAGEWIDTH, self::IMAGEHEIGTH)->getUrl();
    }
}