<?php

namespace Iflair\Productimageinorder\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function isEnabled($websiteId)
    {
        $path='productimageinorder/general/enabled';
        $websiteId =$websiteId?: $this->storeManager->getWebsite()->getId();
        $value=$this->scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,$websiteId);

        if($value==1)
        {
            return true;
        }
        return false;
    }

}