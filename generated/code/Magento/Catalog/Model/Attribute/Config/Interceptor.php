<?php
namespace Magento\Catalog\Model\Attribute\Config;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Attribute\Config
 */
class Interceptor extends \Magento\Catalog\Model\Attribute\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\Attribute\Config\Data $dataStorage)
    {
        $this->___init();
        parent::__construct($dataStorage);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeNames($groupName): array
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributeNames');
        return $pluginInfo ? $this->___callPlugins('getAttributeNames', func_get_args(), $pluginInfo) : parent::getAttributeNames($groupName);
    }
}
