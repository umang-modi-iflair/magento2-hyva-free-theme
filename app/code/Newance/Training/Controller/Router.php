<?php
namespace Newance\Training\Controller;

use Newance\Training\Model\Url;

/**
 * Training Controller Router
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \Newance\Training\Model\PostFactory
     */
    protected $_postFactory;

    /**
     * Category factory
     *
     * @var \Newance\Training\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Brand factory
     *
     * @var \Newance\Training\Model\BrandFactory
     */
    protected $_brandFactory;

    /**
     * Config primary
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Url
     *
     * @var \Newance\Training\Model\Url
     */
    protected $_url;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var int
     */
    protected $_postId;

    /**
     * @var int
     */
    protected $_categoryId;

    /**
     * @var int
     */
    protected $_brandId;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Newance\Training\Model\PostFactory $postFactory
     * @param \Newance\Training\Model\CategoryFactory $categoryFactory
     * @param \Newance\Training\Model\BrandFactory $brandFactory
     * @param \Newance\Training\Model\Url $url
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Url $url,
        \Newance\Training\Model\PostFactory $postFactory,
        \Newance\Training\Model\CategoryFactory $categoryFactory,
        \Newance\Training\Model\BrandFactory $brandFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_postFactory = $postFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_brandFactory = $brandFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
    }

    /**
     * Validate and Match Training Pages and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $_identifier = trim($request->getPathInfo(), '/');

        $pathInfo = explode('/', $_identifier);
        $trainingRoute = $this->_url->getRoute();

        if ($pathInfo[0] != $trainingRoute) {
            return;
        }
        unset($pathInfo[0]);

        // handle trainings/request/
        if ($pathInfo && isset($pathInfo[1]) && $pathInfo[1] == 'request') {
            // Route to your desired controller for this specific pattern
            $request->setModuleName('training')->setControllerName('request')->setActionName('index');
            return $this->actionFactory->create('Magento\Framework\App\Action\Forward', ['request' => $request]);
        }

        switch ($this->_url->getPermalinkType()) {
            case Url::PERMALINK_TYPE_DEFAULT:
                foreach ($pathInfo as $i => $route) {
                    $pathInfo[$i] = $this->_url->getControllerName($route);
                }
                break;
            case Url::PERMALINK_TYPE_SHORT:
                if (isset($pathInfo[1])) {
                    if ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_SEARCH)) {
                        $pathInfo[1] = Url::CONTROLLER_SEARCH;
                    } elseif ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_SUBSCRIBER)) {
                        $pathInfo[1] = Url::CONTROLLER_SUBSCRIBER;
                    } elseif (count($pathInfo) == 1) {
                        if ($postId = $this->_getPostId($pathInfo[1])) {
                            $pathInfo[2] = $pathInfo[1];
                            $pathInfo[1] = Url::CONTROLLER_POST;
                        } elseif ($categoryId = $this->_getCategoryId($pathInfo[1])) {
                            $pathInfo[2] = $pathInfo[1];
                            $pathInfo[1] = Url::CONTROLLER_CATEGORY;
                        } elseif ($brandId = $this->_getBrandId($pathInfo[1])) {
                            $pathInfo[2] = $pathInfo[1];
                            $pathInfo[1] = Url::CONTROLLER_BRAND;
                        }
                    }
                }
                break;
        }

        $identifier = implode('/', $pathInfo);

        $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
        $this->_eventManager->dispatch(
            'newance_training_controller_router_match_before',
            ['router' => $this, 'condition' => $condition]
        );

        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Redirect',
                ['request' => $request]
            );
        }

        if (!$condition->getContinue()) {
            return null;
        }

        $identifier = $condition->getIdentifier();

        $success = false;
        $info = explode('/', $identifier);

        if (!$identifier) {
            $request->setModuleName('training')->setControllerName('index')->setActionName('index');
            $success = true;
        } elseif (count($info) > 1) {
            $store = $this->_storeManager->getStore()->getId();

            switch ($info[0]) {
                case 'post':
                    if (!$postId = $this->_getPostId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('training')->setControllerName('post')->setActionName('view')->setParam('id', $postId);
                    $success = true;
                    break;
                case 'category':
                    if (!$categoryId = $this->_getCategoryId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('training')->setControllerName('category')->setActionName('view')->setParam('id', $categoryId);
                    $success = true;
                    break;
                case 'brand':
                    if (!$brandId = $this->_getBrandId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('training')->setControllerName('brand')->setActionName('view')->setParam('id', $brandId);
                    $success = true;
                    break;
                case 'search':
                    $request->setModuleName('training')->setControllerName('search')->setActionName('index')
                        ->setParam('q', $info[1]);

                    $success = true;
                    break;
                case 'subscriber':
                    if (!$postId = $this->_getPostId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('training')->setControllerName('subscriber')->setActionName('post')->setParam('id', $postId);
                    $success = true;
                    break;
            }
        }

        if (!$success) {
            return null;
        }

        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $_identifier);

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }

    /**
     * Retrieve post id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getPostId($identifier)
    {
        if (is_null($this->_postId)) {
            $post = $this->_postFactory->create();
            $this->_postId = $post->checkIdentifier(
                $identifier,
                $this->_storeManager->getStore()->getId()
            );
        }

        return $this->_postId;
    }

    /**
     * Retrieve category id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getCategoryId($identifier)
    {
        if (is_null($this->_categoryId)) {
            $category = $this->_categoryFactory->create();
            $this->_categoryId = $category->checkIdentifier(
                $identifier,
                $this->_storeManager->getStore()->getId()
            );
        }

        return $this->_categoryId;
    }

    /**
     * Retrieve brand id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getBrandId($identifier)
    {
        if (is_null($this->_brandId)) {
            $brand = $this->_brandFactory->create();
            $this->_brandId = $brand->checkIdentifier(
                $identifier,
                $this->_storeManager->getStore()->getId()
            );
        }

        return $this->_brandId;
    }
}
