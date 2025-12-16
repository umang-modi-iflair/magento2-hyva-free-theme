<?php
namespace Iflair\SizeChart\Controller\Adminhtml\SizeChart;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Iflair\SizeChart\Model\SizeChartFactory;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Iflair_SizeChart::save';

    protected $sizechartFactory;

    public function __construct(
        Action\Context $context,
        SizeChartFactory $sizechartFactory
    ) {
        parent::__construct($context);
        $this->sizechartFactory = $sizechartFactory;
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

        $id = isset($data['sizeunit_id']) ? (int)$data['sizeunit_id'] : null;
        $model = $this->sizechartFactory->create();

        try {
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    throw new LocalizedException(__('This Size Unit no longer exists.'));
                }
            } else {
                unset($data['sizeunit_id']);
            }

            unset($data['created_at']);

            $model->setData($data);
            $model->save();

            $this->messageManager->addSuccessMessage(__('Size Unit saved successfully.'));
            return $resultRedirect->setPath('*/*/');

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['sizeunit_id' => $id]);
        }
    }
}
