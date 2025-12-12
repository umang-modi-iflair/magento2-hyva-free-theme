<?php
namespace Iflair\Aiassistance\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

class OllamaClient
{
    const CONFIG_PATH_OLLAMA_URL    = 'iflair_aiassistance/general/ollama_endpoint';
    const CONFIG_PATH_OLLAMA_MODEL  = 'iflair_aiassistance/general/model_name';
    const CONFIG_PATH_OLLAMA_TIMEOUT = 'iflair_aiassistance/general/timeout';
    const CONFIG_PATH_OLLAMA_APIKEY  = 'iflair_aiassistance/general/api_key';

    protected $scopeConfig;
    protected $curl;
    protected $logger;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Curl $curl,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->curl = $curl;
        $this->logger = $logger;
    }


    /**
     * Send question to Ollama
     */
    public function askModel($question)
    {
        $url     = rtrim($this->scopeConfig->getValue(self::CONFIG_PATH_OLLAMA_URL) ?: 'http://127.0.0.1:11434', '/');
        $model   = $this->scopeConfig->getValue(self::CONFIG_PATH_OLLAMA_MODEL) ?: 'llama3.1';
        $timeout = (int)($this->scopeConfig->getValue(self::CONFIG_PATH_OLLAMA_TIMEOUT) ?: 60);

       if ($timeout < 30) { $timeout = 60; }

        $postData = [
            'model'  => $model,
            'prompt' => $question,
            'stream' => false 
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url . "/api/generate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($postData),
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        if ($error || !$response) {
            return [
                'success' => false,
                'message' => "Ollama error: " . ($error ?: 'No response (timeout)')
            ];
        }

        $parsed = json_decode($response, true);

        return [
            'success'  => true,
            'response' => $parsed['response'] ?? 'Empty response',
            'model'    => $model
        ];
    }


    /**
     * Test Ollama connection and installed models
     */
    public function testConnection()
    {
        try {
            $url   = rtrim($this->scopeConfig->getValue(self::CONFIG_PATH_OLLAMA_URL) ?: 'http://localhost:11434', '/');
            $apiKey = $this->scopeConfig->getValue(self::CONFIG_PATH_OLLAMA_APIKEY);

            $this->curl->setOption(CURLOPT_TIMEOUT, 10);
            $this->curl->addHeader('Content-Type', 'application/json');

            if (!empty($apiKey)) {
                $this->curl->addHeader('Authorization', 'Bearer ' . $apiKey);
            }

            $this->curl->get($url . '/api/tags');
            $status = $this->curl->getStatus();
            $body   = $this->curl->getBody();

            if ($status !== 200) {
                throw new \Exception('Ollama returned HTTP ' . $status . ' - ' . substr($body, 0, 200));
            }

            $decoded = json_decode($body, true);
            return ['success' => true, 'models' => $decoded['models'] ?? $decoded];

        } catch (\Exception $e) {
            $this->logger->error('Ollama test error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}