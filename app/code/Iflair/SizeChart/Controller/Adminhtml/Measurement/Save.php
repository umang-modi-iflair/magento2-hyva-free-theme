<?php
namespace Iflair\SizeChart\Controller\Adminhtml\Measurement;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Iflair\SizeChart\Model\MeasurementFactory;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Iflair_SizeChart::save';

    protected $measurementFactory;

    public function __construct(
        Action\Context $context,
        MeasurementFactory $measurementFactory
    ) {
        parent::__construct($context);
        $this->measurementFactory = $measurementFactory;
    }
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        unset($data['form_key']);

        if (empty($data)) {
            $this->messageManager->addErrorMessage(__('No data to save.'));
            return $resultRedirect->setPath('*/*/');
        }

        $id = isset($data['measurement_id']) ? (int)$data['measurement_id'] : null;
        $model = $this->measurementFactory->create();

        try {
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    throw new LocalizedException(__('This Size Unit no longer exists.'));
                }
            } else {
                unset($data['measurement_id']);
            }

            unset($data['created_at']);

            $model->setData($data);
            $model->save();

            $this->messageManager->addSuccessMessage(__('Measurement saved successfully.'));
            return $resultRedirect->setPath('*/*/');

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['measurement_id' => $id]);
        }
    }
}
