<?php
namespace Iflair\CountDownTimer\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Iflair\CountDownTimer\Model\ResourceModel\Widget\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\RequestInterface;
use Iflair\CountDownTimer\Helper\Data;
use Magento\Framework\Registry;

class Countdown implements ArgumentInterface
{
    protected $widgetCollectionFactory;
    protected $dateTime;
    protected $request;
    protected $helper;
    protected $registry;
    protected $timezone;

    private $activeWidget = false;

    public function __construct(
        CollectionFactory $widgetCollectionFactory,
        DateTime $dateTime,
        TimezoneInterface $timezone,
        RequestInterface $request,
        Data $helper,
        Registry $registry
    ) {
        $this->widgetCollectionFactory = $widgetCollectionFactory;
        $this->dateTime = $dateTime;
        $this->timezone = $timezone;
        $this->request = $request;
        $this->helper = $helper;
        $this->registry = $registry;
    }

    public function getActiveCountdownWidgets(): array
    {
        if (!$this->helper->isEnabled()) {
            return [];
        }

        $pageType = $this->getCurrentPageType();
        if (!$pageType) {
            return [];
        }

        $currentTime = $this->dateTime->gmtDate('Y-m-d H:i:s');
        $validWidgets = [];

        $collection = $this->widgetCollectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('start_time', ['lteq' => $currentTime])
            ->addFieldToFilter('end_time', ['gt' => $currentTime])
            ->setOrder('start_time', 'DESC');

        foreach ($collection as $item) {
            if (!$item->getId()) {
                continue;
            }

            if (!$this->isWidgetAllowedOnPage($item, $pageType)) {
                continue;
            }

            if ($pageType === 'category' && !$this->isWidgetAllowedForCategories($item)) {
                continue;
            }

            if ($pageType === 'product' && !$this->isWidgetAllowedForProduct($item)) {
                continue;
            }

            $validWidgets[] = $item;
        }
        
        return $validWidgets;
    }

    public function getEndTimeIso()
    {
        $widget = $this->getActiveCountdownWidget();
        if (!$widget) {
            return null;
        }

        return $this->dateTime->date(\DateTime::ATOM, $widget->getEndTime());
    }

    public function getInitialSeconds()
    {
        $widget = $this->getActiveCountdownWidget();
        if (!$widget) {
            return 0;
        }

        $endGmt = $this->dateTime->gmtTimestamp($widget->getEndTime());
        $nowGmt = $this->dateTime->gmtTimestamp();

        return max(0, $endGmt - $nowGmt);
    }

    public function getWidgetName()
    {
        $widget = $this->getActiveCountdownWidget();
        return $widget ? $widget->getName() : "";
    }

    public function getActiveCountdownWidget()
    {
        if ($this->activeWidget !== false) {
            return $this->activeWidget;
        }

        $widgets = $this->getActiveCountdownWidgets();
        $this->activeWidget = $widgets[0] ?? null;

        return $this->activeWidget;
    }

    public function getEndTimeIsoForWidget($widget)
    {
        if (!$widget || !$widget->getEndTime()) {
            return null;
        }

        $utcDate = new \DateTime($widget->getEndTime(), new \DateTimeZone('UTC'));
        return $this->timezone->date($utcDate)->format(\DateTime::ATOM);
    }

    public function getInitialSecondsForWidget($widget)
    {
        if (!$widget || !$widget->getEndTime()) {
            return 0;
        }

        $endTimestamp = $this->dateTime->gmtTimestamp($widget->getEndTime());
        $now = $this->dateTime->gmtTimestamp();

        return max(0, $endTimestamp - $now);
    }

    public function getWidgetNameForWidget($widget): string
    {
        return $widget && $widget->getName() ? $widget->getName() : '';
    }

    private function isWidgetAllowedForCategories($widget): bool
    {
        $widgetCategoryIds = $this->getWidgetCategoryIds($widget);

        if (empty($widgetCategoryIds)) {
            return true;
        }

        $contextCategoryIds = $this->getContextCategoryIds();

        if (empty($contextCategoryIds)) {
            return false;
        }

        return (bool)array_intersect($widgetCategoryIds, $contextCategoryIds);
    }

    private function getWidgetCategoryIds($widget): array
    {
        $raw = $widget->getData('category_ids');

        if (!$raw) {
            return [];
        }

        $ids = array_filter(
            array_map('trim', explode(',', $raw)),
            static function ($value) {
                return $value !== '';
            }
        );

        return array_map('intval', $ids);
    }

    private function getContextCategoryIds(): array
    {
        $ids = [];

        $currentCategory = $this->registry->registry('current_category');
        if ($currentCategory && $currentCategory->getId()) {
            $ids[] = (int)$currentCategory->getId();
        }

        $currentProduct = $this->registry->registry('current_product');
        if ($currentProduct) {
            $productCategoryIds = (array)$currentProduct->getCategoryIds();
            $ids = array_merge($ids, $productCategoryIds);
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    private function getCurrentPageType(): ?string
    {
        $action = $this->request->getFullActionName();

        if ($action === 'cms_index_index') {
            return 'home';
        }

        if (strpos($action, 'catalog_category') !== false) {
            return 'category';
        }

        if (strpos($action, 'catalog_product') !== false) {
            return 'product';
        }

        if ($action === 'checkout_cart_index') {
            return 'cart';
        }

        if (strpos($action, 'checkout') !== false) {
            return 'checkout';
        }

        return null;
    }

    private function isWidgetAllowedOnPage($widget, string $pageType): bool
    {
        $locations = $this->getWidgetDisplayLocations($widget);

        if (empty($locations) || in_array('all', $locations, true)) {
            return true;
        }

        return in_array($pageType, $locations, true);
    }

    private function getWidgetDisplayLocations($widget): array
    {
        $raw = $widget->getData('display_on');

        if (!$raw) {
            return [];
        }

        if (is_array($raw)) {
            $values = $raw;
        } else {
            $values = explode(',', (string)$raw);
        }

        $values = array_map('trim', $values);
        $values = array_filter($values, static function ($value) {
            return $value !== '';
        });

        return array_values($values);
    }

    private function isWidgetAllowedForProduct($widget): bool
    {
        $productIds = $this->getWidgetProductIds($widget);

        if (empty($productIds)) {
            return true;
        }

        $currentProduct = $this->registry->registry('current_product');
        if (!$currentProduct || !$currentProduct->getId()) {
            return false;
        }

        return in_array((int)$currentProduct->getId(), $productIds, true);
    }

    private function getWidgetProductIds($widget): array
    {
        $raw = $widget->getData('product_ids') ?: $widget->getData('product_id');

        if (!$raw) {
            return [];
        }

        if (is_array($raw)) {
            $values = $raw;
        } else {
            $values = explode(',', (string)$raw);
        }

        $values = array_map('trim', $values);
        $values = array_filter($values, static function ($value) {
            return $value !== '';
        });

        return array_map('intval', $values);
    }
}
