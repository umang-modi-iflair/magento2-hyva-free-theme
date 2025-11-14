<?php
namespace Newance\Training\Controller\Adminhtml;

/**
 * Admin training subscriber edit controller
 */
class Subscriber extends Actions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'training_subscriber_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Newance_Training::subscriber';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Newance\Training\Model\Subscriber';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Newance_Training::subscriber';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';
}
