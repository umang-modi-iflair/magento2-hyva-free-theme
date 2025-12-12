<?php
namespace Iflair\Aiassistance\Api;

interface AskInterface
{
    /**
     * Ask AI Assistant a question
     *
     * @param string $question User question
     * @return array
     */
    public function ask($question);

    /**
     * Test Ollama connection
     *
     * @return array
     */
    public function testConnection();
}