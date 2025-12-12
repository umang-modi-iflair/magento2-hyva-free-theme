<?php
namespace Iflair\CountDownTimer\Controller\Adminhtml\CountDownTimer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Iflair\CountDownTimer\Model\WidgetFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Iflair_CountDownTimer::save';

    protected $widgetFactory;
    protected $timezone;

    public function __construct(
        Context $context,
        WidgetFactory $widgetFactory,
        TimezoneInterface $timezone
    ) {
        $this->widgetFactory = $widgetFactory;
        $this->timezone = $timezone;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            $this->messageManager->addErrorMessage(__('No data to save.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $shouldSaveCategories = !isset($data['display_on'])
            || (is_array($data['display_on']) && in_array('category', $data['display_on'], true))
            || $data['display_on'] === 'category';

        if ($shouldSaveCategories && isset($data['category_ids']) && is_array($data['category_ids'])) {
            $data['category_ids'] = implode(',', array_filter($data['category_ids']));
        } else {
            if (isset($data['category_ids'])) {
                unset($data['category_ids']);
            }
        }

        // Only save product_ids if the display_on value is 'product' (i.e., for product pages)
        if (isset($data['display_on']) && $data['display_on'] == 'product' && isset($data['product_ids']) && is_array($data['product_ids'])) {
            $data['product_ids'] = implode(',', array_filter($data['product_ids']));
        } else {
            // For pages where product selection is not applicable, unset product_ids
            if (isset($data['product_ids'])) {
                unset($data['product_ids']);
            }
        }

        if (isset($data['start_time']) && $data['start_time']) {
            $data['start_time'] = $this->timezone->convertConfigTimeToUtc($data['start_time']);
        }
        if (isset($data['end_time']) && $data['end_time']) {
            $data['end_time'] = $this->timezone->convertConfigTimeToUtc($data['end_time']);
        }

        $id = $this->getRequest()->getParam('widget_id');
        $model = $this->widgetFactory->create();

        try {
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    throw new LocalizedException(__('This widget no longer exists.'));
                }
            } else {
                unset($data['widget_id']);
            }

            $model->addData($data);
            $model->save();

            $this->messageManager->addSuccessMessage(__('Widget saved successfully.'));

            if ($this->getRequest()->getParam('back')) {
                return $this->resultRedirectFactory
                    ->create()
                    ->setPath('*/*/edit', ['widget_id' => $model->getId()]);
            }

            return $this->resultRedirectFactory->create()->setPath('*/*/');

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory
                ->create()
                ->setPath('*/*/edit', ['widget_id' => $id]);
        }
    }
}
