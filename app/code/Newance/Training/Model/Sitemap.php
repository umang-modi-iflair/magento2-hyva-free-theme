<?php
namespace Newance\Training\Model;

/**
 * Overide sitemap
 */
class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * Initialize sitemap items
     *
     * @return void
     */
    protected function _initSitemapItems()
    {
        parent::_initSitemapItems();

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => 'weekly',
                'priority' => '0.25',
                'collection' =>  \Magento\Framework\App\ObjectManager::getInstance()->create(
                    'Newance\Training\Model\Category'
                )->getCollection($this->getStoreId())
                    ->addStoreFilter($this->getStoreId())
                    ->addActiveFilter(),
            ]
        );

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => 'weekly',
                'priority' => '0.25',
                'collection' =>  \Magento\Framework\App\ObjectManager::getInstance()->create(
                    'Newance\Training\Model\Post'
                )->getCollection($this->getStoreId())
                    ->addStoreFilter($this->getStoreId())
                    ->addActiveFilter(),
            ]
        );

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => 'weekly',
                'priority' => '0.25',
                'collection' =>  \Magento\Framework\App\ObjectManager::getInstance()->create(
                    'Newance\Training\Model\Brand'
                )->getCollection($this->getStoreId())
                    ->addStoreFilter($this->getStoreId())
                    ->addActiveFilter(),
            ]
        );
    }
}
