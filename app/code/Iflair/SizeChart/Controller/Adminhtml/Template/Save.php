<?php
namespace Iflair\SizeChart\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Iflair\SizeChart\Model\TemplateFactory;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Iflair_SizeChart::save';

    protected $templateFactory;

    public function __construct(
        Action\Context $context,
        TemplateFactory $templateFactory
    ) {
        parent::__construct($context);
        $this->templateFactory = $templateFactory;
    }
     public function execute()
    {
    $data = $this->getRequest()->getPostValue();
    $resultRedirect = $this->resultRedirectFactory->create();

    if (empty($data)) {
        return $resultRedirect->setPath('*/*/');
    }

    $id = isset($data['template_id']) ? (int)$data['template_id'] : null;
    $model = $this->templateFactory->create();

    try {
        if ($id) {
            $model->load($id);
        }

        if (isset($data['size_unit']) && is_array($data['size_unit'])) {
            $data['size_unit'] = implode(',', $data['size_unit']);
        }
        if (isset($data['measurement']) && is_array($data['measurement'])) {
            $data['measurement'] = implode(',', $data['measurement']);
        }

        $model->setData($data);
        $model->setData('size_chart_data', $data['size_chart_data'] ?? '');
        $model->save();

        $this->messageManager->addSuccessMessage(__('Template saved successfully.'));
        
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/index', ['template_id' => $model->getId()]);
        }
        return $resultRedirect->setPath('*/*/');

    } catch (\Exception $e) {
        $this->messageManager->addErrorMessage($e->getMessage());
        return $resultRedirect->setPath('*/*/');
    }
}
}
