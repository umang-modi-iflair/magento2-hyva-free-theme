<?php
namespace Newance\Training\Model;

/**
 * Subscriber model
 *
 * @method \Newance\Training\Model\ResourceModel\Subscriber _getResource()
 * @method \Newance\Training\Model\ResourceModel\Subscriber getResource()
 * @method string getName()
 * @method $this setName(string $value)
 * @method string getEmail()
 * @method $this setEmail(string $value)
 * @method string getMessage()
 * @method $this setMessage(string $value)
 */
class Subscriber extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Subscribers Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'newance_training_subscriber';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'training_subscriber';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Newance\Training\Model\ResourceModel\Subscriber');
    }

    /**
     * Retrieve model title
     *
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Subscriber' : 'Subscribers';
    }

    /**
     * Retrieve true if subscriber is active
     *
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve subscriber publish date using format
     *
     * @param  string $format
     * @return string
     */
    public function getPublishDate($format = 'd/m/Y H:i')
    {
        return date($format, strtotime($this->getDate()));
    }

    /**
     * Retrieve available subscriber statuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_DISABLED => __('Disabled'),
            self::STATUS_ENABLED => __('Enabled')
        ];
    }
}
