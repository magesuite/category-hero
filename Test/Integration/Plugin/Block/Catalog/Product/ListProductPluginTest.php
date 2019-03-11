<?php

namespace MageSuite\CategoryHero\Test\Integration\Plugin\Block\Catalog\Product;

class ListProductPluginTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $pluginName = 'category_hero_product_list_plugin';

    /**
     * @var string
     */
    protected $categoryRegistryKey = 'current_category';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $categoryModel;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->categoryModel = $this->objectManager->create(\Magento\Catalog\Model\Category::class);
        $this->registry->unregister($this->categoryRegistryKey);
    }

    /**
     * @return mixed
     */
    protected function getCatalogProductListBlockPlugins()
    {
        /** @var \Magento\TestFramework\Interception\PluginList $pluginList */
        $pluginList = $this->objectManager->get(\Magento\TestFramework\Interception\PluginList::class);
        return $pluginList->get(\Magento\Catalog\Block\Product\ListProduct::class, []);
    }

    /**
     * @param int $categoryId
     */
    protected function loadAndRegisterCategory($categoryId)
    {
        $category = $this->objectManager
            ->create(\Magento\Catalog\Model\Category::class)
            ->load($categoryId);
        $this->registry->unregister($this->categoryRegistryKey);
        $this->registry->register($this->categoryRegistryKey, $category);
    }

    /**
     * @return \Magento\Catalog\Block\Product\ListProduct
     */
    protected function getProductListBlock()
    {
        return $this->objectManager->create(\Magento\Catalog\Block\Product\ListProduct::class);
    }

    /**
     * @magentoAppArea frontend
     */
    public function testPluginIsConfiguredToInterceptCallsInFrontendArea()
    {
        $plugins = $this->getCatalogProductListBlockPlugins();
        $this->assertSame(
            \MageSuite\CategoryHero\Plugin\Block\Catalog\Product\ListProductPlugin::class,
            $plugins[$this->pluginName]['instance']
        );
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testPluginIsConfiguredNotToInterceptCallsInAdminhtmlArea()
    {
        $plugins = $this->getCatalogProductListBlockPlugins();
        $this->assertArrayNotHasKey($this->pluginName, $plugins);
    }

    /**
     * @param int $categoryId
     * @param boolean $heroEnabled
     * @magentoDataFixture loadFixture
     * @magentoAppArea frontend
     * @dataProvider categoryProvider
     */
    public function testPluginReturnsValueOfEnableHeroProductAttributeWhenCategoryIsRegistered($categoryId, $heroEnabled)
    {
        $this->loadAndRegisterCategory($categoryId);
        $this->assertSame(
            $heroEnabled,
            $this->getProductListBlock()->getIsHeroEnabled()
        );
    }

    /**
     * @magentoAppArea frontend
     */
    public function testPluginReturnsFalseWhenNoCategoryIsRegistered()
    {
        $this->assertFalse($this->getProductListBlock()->getIsHeroEnabled());
    }

    /**
     * @return array
     */
    public function categoryProvider()
    {
        return [
            [3, false],
            [4, false],
            [5, false],
            [6, false],
            [7, false],
            [8, false],
        ];
    }

    public static function loadFixture()
    {
        include __DIR__ . '/../../../../_files/categories_no_products.php';
    }

    public static function loadFixtureRollback()
    {
        include __DIR__ . '/../../../../_files/categories_no_products_rollback.php';
    }
}
