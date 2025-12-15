<?php
namespace Iflair\Delivery\Model\Data;

use Iflair\Delivery\Api\Data\PincodeUpdateInterface;

class PincodeUpdate implements PincodeUpdateInterface
{
    protected $minDeliveryDate;
    protected $maxDeliveryDate;
    protected $message;

    public function getMinDeliveryDate()
    {
        return $this->minDeliveryDate;
    }

    public function setMinDeliveryDate($value)
    {
        $this->minDeliveryDate = $value;
        return $this;
    }

    public function getMaxDeliveryDate()
    {
        return $this->maxDeliveryDate;
    }

    public function setMaxDeliveryDate($value)
    {
        $this->maxDeliveryDate = $value;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($value)
    {
        $this->message = $value;
        return $this;
    }
}
