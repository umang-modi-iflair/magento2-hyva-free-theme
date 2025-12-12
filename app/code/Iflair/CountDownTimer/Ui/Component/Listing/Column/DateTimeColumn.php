<?php

namespace Iflair\CountDownTimer\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class DateTimeColumn extends Column
{
    protected $timezone;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        TimezoneInterface $timezone,
        array $components = [],
        array $data = []
    ) {
        $this->timezone = $timezone;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['start_time'])) {
                    $item['start_time'] = $this->timezone->formatDateTime(
                        $item['start_time'],
                        \IntlDateFormatter::MEDIUM,
                        \IntlDateFormatter::MEDIUM
                    );
                }
                if (isset($item['end_time'])) {
                    $item['end_time'] = $this->timezone->formatDateTime(
                        $item['end_time'],
                        \IntlDateFormatter::MEDIUM,
                        \IntlDateFormatter::MEDIUM
                    );
                }
            }
        }
        return $dataSource;
    }
}
