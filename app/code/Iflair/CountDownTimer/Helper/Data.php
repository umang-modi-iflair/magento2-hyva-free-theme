<?php
namespace Iflair\CountDownTimer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLE = 'countdown_config/general/enable';
    const XML_PATH_DISPLAY_ON = 'countdown_config/general/display_on';


    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getDisplayPages($storeId = null)
    {
        return explode(',', (string)$this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_ON,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }
}
