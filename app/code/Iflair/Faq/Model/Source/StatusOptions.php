<?php
namespace Iflair\Faq\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class StatusOptions implements ArrayInterface
{
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => self::STATUS_APPROVED, 'label' => __('Approved')],
        ];
    }

    public static function getOptionArray()
    {
        return [
            self::PUBLIC_VISIBILITY  => __('Pending'),
            self::PRIVATE_VISIBILITY => __('Approved')
        ];
    }
}