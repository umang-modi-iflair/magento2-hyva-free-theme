<?php
namespace Iflair\Faq\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class VisibilityOptions implements ArrayInterface
{
    const VISIBILITY_PUBLIC = 1;
    const VISIBILITY_HIDDEN = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::VISIBILITY_PUBLIC, 'label' => __('Public')],
            ['value' => self::VISIBILITY_HIDDEN, 'label' => __('Hidden')],
        ];
    }

    public function getOptionArray()
    {
        return [
            self::VISIBILITY_PUBLIC => __('Public'),
            self::VISIBILITY_HIDDEN => __('Hidden')
        ];
    }
}
