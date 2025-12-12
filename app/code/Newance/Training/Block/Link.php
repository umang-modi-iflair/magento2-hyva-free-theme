<?php

namespace Newance\Training\Block;

/**
 * Class Link
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Newance\Training\Model\Url
     */
    protected $_url;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Newance\Training\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Newance\Training\Model\Url $url,
        array $data = []
    ) {
        $this->_url = $url;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_url->getBaseUrl();
    }
}
