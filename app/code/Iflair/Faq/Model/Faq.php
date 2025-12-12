<?php

namespace Iflair\Faq\Model;

use Magento\Framework\Model\AbstractModel;

class Faq extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Iflair\Faq\Model\ResourceModel\Faq::class);
    }

    /**
     * Get FAQ ID
     */
    public function getFaqId()
    {
        return $this->getData('faq_id');
    }

    /**
     * Get Question
     */
    public function getQuestion()
    {
        return $this->getData('question');
    }

    /**
     * Get Answer
     */
    public function getAnswer()
    {
        return $this->getData('answer');
    }

    /**
     * Get Store View
     */
    public function getStoreView()
    {
        return $this->getData('store_view');
    }

    /**
     * Get URL Key
     */
    public function getUrlKey()
    {
        return $this->getData('url_key');
    }

    /**
     * Get Status
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Get Visibility
     */
    public function getVisibility()
    {
        return $this->getData('visibility');
    }
}