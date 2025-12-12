<?php
namespace Newance\Training\Controller\Adminhtml;

/**
 * Admin training brand edit controller
 */
class Brand extends Actions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'training_brand_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Newance_Training::brand';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Newance\Training\Model\Brand';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Newance_Training::brand';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';
}
