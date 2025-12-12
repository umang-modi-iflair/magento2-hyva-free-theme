<?php
namespace Newance\Training\Controller\Adminhtml;

/**
 * Admin training post edit controller
 */
class Post extends Actions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'training_post_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Newance_Training::post';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Newance\Training\Model\Post';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Newance_Training::post';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';

    /**
     * Save request params key
     * @var string
     */
    protected $_paramsHolder 	= 'post';
}
