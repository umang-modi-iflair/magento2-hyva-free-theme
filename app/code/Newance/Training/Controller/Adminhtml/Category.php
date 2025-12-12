<?php
namespace Newance\Training\Controller\Adminhtml;

/**
 * Admin training category edit controller
 */
class Category extends Actions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'training_category_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Newance_Training::category';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Newance\Training\Model\Category';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Newance_Training::category';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';
}
