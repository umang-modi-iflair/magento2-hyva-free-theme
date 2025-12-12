<?php

namespace Newance\Training\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Training install data
 */
class InstallData implements InstallDataInterface
{
    /**
     * Category factory
     *
     * @var \Newance\Training\Model\CategoryFactory
     */
    private $_categoryFactory;

    /**
     * Post factory
     *
     * @var \Newance\Training\Model\PostFactory
     */
    private $_postFactory;

    /**
     * Subscriber factory
     *
     * @var \Newance\Training\Model\SubscriberFactory
     */
    private $_subscriberFactory;

    /**
     * Init
     *
     * @param \Newance\Training\Model\CategoryFactory $categoryFactory
     * @param \Newance\Training\Model\PostFactory $postFactory
     * @param \Newance\Training\Model\SubscriberFactory $subscriberFactory
     */
    public function __construct(
        \Newance\Training\Model\CategoryFactory $categoryFactory,
        \Newance\Training\Model\PostFactory $postFactory,
        \Newance\Training\Model\SubscriberFactory $subscriberFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_postFactory = $postFactory;
        $this->_subscriberFactory = $subscriberFactory;
    }

    /**
     * Install data
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $categoryIds = $this->createCategories();
        $this->createPosts($categoryIds);
    }

    /**
     * Create categories (and subcategories)
     *
     * @return array $categoryIds
     */
    private function createCategories()
    {
        $categoryIds = [];

        for ($i = 1; $i <= 3; $i++) {
            $data = [
                'title' => 'Cat ' . $i,
                'meta_keywords' => 'Meta Keywords',
                'meta_description' => 'Meta Description',
                'identifier' => 'cat-' . $i,
                'content_heading' => 'Cat ' . $i,
                'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sollicitudin pellentesque odio, non lobortis metus varius nec. Maecenas eu varius nulla. Etiam vitae ligula quis quam dictum congue.</p>',
                'path' => 0,
                'position' => 0,
                'is_active' => 1,
                'store_ids' => [0]
            ];

            $category = $this->_categoryFactory->create()->setData($data)->save();
            $categoryIds[] = $category->getId();

            for ($y = 1; $y <= 3; $y++) {
                $data = [
                    'title' => 'Cat ' . $i . '-' . $y,
                    'meta_keywords' => 'Meta Keywords',
                    'meta_description' => 'Meta Description',
                    'identifier' => 'cat-' . $i . '-' . $y,
                    'content_heading' => 'Cat ' . $i . '-' . $y,
                    'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sollicitudin pellentesque odio, non lobortis metus varius nec. Maecenas eu varius nulla. Etiam vitae ligula quis quam dictum congue.</p>',
                    'path' => $category->getId(),
                    'position' => 0,
                    'is_active' => 1,
                    'store_ids' => [0]
                ];

                $subCategory = $this->_categoryFactory->create()->setData($data)->save();
                $categoryIds[] = $subCategory->getId();
            }
        }

        return $categoryIds;
    }

    /**
     * Create posts
     */
    private function createPosts($categoryIds)
    {
        $currentDate = new \DateTime();
        $currentDate->modify('+1 month');

        for ($i = 1; $i <= 12; $i++) {
            $data = [
                'title' => 'Hello world! - ' . $i,
                'meta_keywords' => 'Meta Keywords',
                'meta_description' => 'Meta Description',
                'identifier' => 'hello-world-' . $i,
                'location' => 'Steenweg Deinze 150/20, Nazareth',
                'hour' => '19u30',
                'amount' => '120',
                'remaining_places' => '10',
                'content_heading' => 'Hello world! - ' . $i,
                'short_content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sollicitudin pellentesque odio, non lobortis metus varius nec. Maecenas eu varius nulla. Etiam vitae ligula quis quam dictum congue.</p>',
                'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sollicitudin pellentesque odio, non lobortis metus varius nec. Maecenas eu varius nulla. Etiam vitae ligula quis quam dictum congue. Maecenas accumsan, velit a sodales dictum, lacus dolor molestie arcu, sit amet viverra ex odio et libero. Pellentesque condimentum pretium dolor, at hendrerit metus vulputate et. Donec in congue risus. Nulla dictum tincidunt elementum. Morbi dolor massa, mollis id pellentesque at, semper nec dui. Ut nec faucibus lectus, quis congue nunc. Pellentesque tempus massa in lorem pharetra, pulvinar laoreet ex rutrum.</p><p>Proin tristique metus at felis pretium bibendum. Integer eu turpis dictum, aliquet quam vitae, rhoncus tortor. Donec ac aliquet tellus. Fusce dapibus ante sit amet ipsum dictum, non fermentum nulla laoreet. Nullam euismod felis nec orci placerat cursus. Aliquam at magna lacinia nisi pellentesque bibendum. Donec faucibus ligula massa, a vehicula lacus cursus dictum. In bibendum tincidunt massa, eget eleifend felis dignissim non. Praesent congue cursus nibh ut pretium. Donec egestas enim nec purus viverra interdum. Nunc ut porttitor diam, quis ultricies tortor.</p>',
                'store_ids' => [0],
                'categories' => [array_rand($categoryIds, rand(1, 3))],
                'publish_time' => $currentDate->format('Y-m-d H:i:s'), //timestamp field, daarom deze format
            ];

            $currentDate->modify('+1 month');

            $this->_postFactory->create()->setData($data)->save();
        }
    }
}
