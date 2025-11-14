<?php

namespace Newance\Training\Block\Sidebar;

/**
 * Training sidebar categories block
 */
class Search extends \Magento\Framework\View\Element\Template
{
    use Widget;

    /**
     * @var \Newance\Training\Model\Url
     */
    protected $_url;

    /**
     * @var string
     */
    protected $_widgetKey = 'search';

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Newance\Training\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Newance\Training\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_url = $url;
    }

    /**
     * Retrieve query
     * @return string
     */
    public function getQuery()
    {
        return urldecode($this->getRequest()->getParam('q', ''));
    }

    /**
     * Retrieve serch form action url
     * @return string
     */
    public function getFormUrl()
    {
        return $this->_url->getUrl('', \Newance\Training\Model\Url::CONTROLLER_SEARCH);
    }
}
