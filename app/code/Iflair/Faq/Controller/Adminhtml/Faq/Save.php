<?php
namespace Iflair\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Iflair\Faq\Model\FaqFactory;
use Iflair\Faq\Model\ResourceModel\Faq as FaqResource;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Iflair_Faq::faq_save';

    protected FaqFactory $faqFactory;
    protected FaqResource $faqResource;

    public function __construct(
        Action\Context $context,
        FaqFactory $faqFactory,
        FaqResource $faqResource
    ) {
        $this->faqFactory = $faqFactory;
        $this->faqResource = $faqResource;
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            $this->messageManager->addErrorMessage(__('No data to save.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $selectedProducts = $this->getRequest()->getParam('selected_products', []);
            if (!is_array($selectedProducts)) {
                $selectedProducts = [];
            }

            $id = $this->getRequest()->getParam('id');
            $faq = $this->faqFactory->create();

            if ($id) {
                $this->faqResource->load($faq, $id);
                if (!$faq->getId()) {
                    throw new LocalizedException(__('This FAQ no longer exists.'));
                }
            }

            if (empty($data['question']) || trim($data['question']) === '') {
                throw new LocalizedException(__('Question is required.'));
            }
            if (empty($data['answer']) || trim($data['answer']) === '') {
                throw new LocalizedException(__('Answer is required.'));
            }

            if (isset($data['store_view']) && is_array($data['store_view'])) {
                $data['store_view'] = implode(',', array_filter($data['store_view']));
            }
            if (isset($data['customer_group']) && is_array($data['customer_group'])) {
                $data['customer_group'] = implode(',', array_filter($data['customer_group']));
            }

            $data['product_ids'] = !empty($selectedProducts) ? implode(',', $selectedProducts) : '';

            $faq->addData($data);
            $this->faqResource->save($faq);

            $this->saveSelectedProducts((int)$faq->getId(), $selectedProducts);

            $this->messageManager->addSuccessMessage(__('FAQ saved successfully.'));

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $faq->getId()]);
            }

            return $resultRedirect->setPath('*/*/index');

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the FAQ.'));
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
    }

    /**
     * Save selected products in link table
     *
     * @param int $faqId
     * @param array $productIds
     * @return void
     */
    /**
 * Save selected products for FAQ
 *
 * @param int   $faqId
 * @param array $productIds
 * @return void
 */
protected function saveSelectedProducts(int $faqId, array $productIds): void
{
    if (!$faqId) {
        return;
    }

    $connection = $this->faqResource->getConnection();
    $linkTable  = $this->faqResource->getTable('iflair_faq_product');

    $connection->delete($linkTable, ['faq_id = ?' => $faqId]);

    if (!empty($productIds) && is_array($productIds)) {

        $productIds = array_filter($productIds, function ($id) {
            return !empty($id) && is_numeric($id);
        });

        if (empty($productIds)) {
            return;
        }

        $insertData = [];

        foreach ($productIds as $productId) {
            $insertData[] = [
                'faq_id'     => (int)$faqId,
                'product_id' => (int)$productId
            ];
        }

        try {
            $connection->insertMultiple($linkTable, $insertData);
        } catch (\Exception $e) {
            $this->logger->error('FAQ Product Save Error: ' . $e->getMessage());
        }
    }
}

}
