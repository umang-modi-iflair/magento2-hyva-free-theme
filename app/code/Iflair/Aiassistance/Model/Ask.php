<?php
namespace Iflair\Aiassistance\Model;

use Iflair\Aiassistance\Api\AskInterface;
use Iflair\Aiassistance\Model\OllamaClient;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Ask implements AskInterface
{
    protected $ollamaClient;
    protected $productCollectionFactory;

    public function __construct(
        OllamaClient $ollamaClient,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->ollamaClient = $ollamaClient;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function ask($question)
    {
        $product = $this->detectProductFromQuestion($question);

        if ($product && $product->getId()) {
            $prompt = $this->buildDynamicPrompt($product, $question);
        } else {
            $prompt = $question;
        }

        $resp = $this->ollamaClient->askModel($prompt);

        if (!isset($resp['success']) || !$resp['success']) {
            return [
                'success' => false,
                'message' => $resp['message'] ?? 'No response from AI',
                'model'   => null
            ];
        }

        return [
            'success' => true,
            'message' => $resp['response'],
            'model'   => $resp['model']
        ];
    }

    public function testConnection()
    {
        return $this->ollamaClient->testConnection();
    }

    /**
     * Detect product from question using product name words
     */
    private function detectProductFromQuestion($question)
    {
        $words = preg_split('/[^a-zA-Z0-9]+/', strtolower($question));

        $words = array_filter($words, function ($w) {
            return strlen($w) > 2;
        });

        if (empty($words)) {
            return null;
        }

        $conditions = [];
        foreach ($words as $w) {
            $conditions[] = ['attribute' => 'name', 'like' => "%$w%"];
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'price', 'description']);
        $collection->addAttributeToFilter($conditions, 'OR');
        $collection->setPageSize(1);

        $product = $collection->getFirstItem();

        if (!$product || !$product->getId()) {
            return null;
        }

        return $product;
    }

    /**
     * Build dynamic Ollama prompt with product data
     */
    private function buildDynamicPrompt($product, $question)
    {
        $name  = $product->getName();
        $price = $product->getFinalPrice();

        $desc = strip_tags($product->getDescription());
        $desc = substr($desc, 0, 500);

        return "
            You are a Magento Product Expert. 
            Answer ONLY using the product details provided below.

            Product Details:
            - Name: $name
            - Price: $price
            - Description: $desc

            User Question: \"$question\"

            Give a clear, helpful, and accurate answer based ONLY on the above product details.
            ";
    }
}
