<?php

namespace Newance\Training\Block\Post;

/**
 * Abstract post мшуц block
 */
abstract class AbstractPost extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Newance\Training\Model\Post
     */
    protected $_post;

    /**
     * Page factory
     *
     * @var \Newance\Training\Model\PostFactory
     */
    protected $_postFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Newance\Training\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $post
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\PageFactory $postFactory
     * @param \Newance\Training\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Newance\Training\Model\Post $post,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Newance\Training\Model\PostFactory $postFactory,
        \Newance\Training\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_post = $post;
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_postFactory = $postFactory;
        $this->_url = $url;
    }

    /**
     * Retrieve post instance
     *
     * @return \Newance\Training\Model\Post
     */
    public function getPost()
    {
        if (!$this->hasData('post')) {
            $this->setData(
                'post',
                $this->_coreRegistry->registry('current_training_post')
            );
        }
        return $this->getData('post');
    }

    /**
     * Retrieve post short content
     *
     * @return string
     */
    public function getShortContent()
    {
        $content = $this->getPost()->getData()['short_content'];

        return $this->_filterProvider->getPageFilter()->filter($content);
    }

    /**
     * Retrieve post home short content
     *
     * @return string
     */
    public function getHomeShortContent()
    {
        $homeContent = $this->getPost()->getData()['home_short_content'];

        return $this->_filterProvider->getPageFilter()->filter($homeContent);
    }

    /**
     * Retrieve post content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_filterProvider->getPageFilter()->filter(
            $this->getPost()->getContent()
        );

        return $this->getData($k);
    }
}
