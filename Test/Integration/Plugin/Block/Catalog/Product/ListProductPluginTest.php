<?php
/**
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\CategoryHero\Test\Integration\Plugin\Block\Catalog\Product;

use MageSuite\CategoryHero\Plugin\Block\Catalog\Product\ListProductPlugin;
use Magento\Catalog\Block\Product\ListProduct as ProductListBlock;
use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\TestFramework\Interception\PluginList;
use Magento\TestFramework\ObjectManager;

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
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Category
     */
    protected $categoryModel;

    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
        $this->registry = $this->objectManager->get(Registry::class);
        $this->categoryModel = $this->objectManager->create(Category::class);
        $this->registry->unregister($this->categoryRegistryKey);
    }

    /**
     * @return mixed
     */
    protected function getCatalogProductListBlockPlugins()
    {
        /** @var PluginList $pluginList */
        $pluginList = $this->objectManager->get(PluginList::class);
        return $pluginList->get(ProductListBlock::class, []);
    }

    /**
     * @param int $categoryId
     */
    protected function loadAndRegisterCategory($categoryId)
    {
        $category = $this->objectManager
            ->create(Category::class)
            ->load($categoryId);
        $this->registry->unregister($this->categoryRegistryKey);
        $this->registry->register($this->categoryRegistryKey, $category);
    }

    /**
     * @return ProductListBlock
     */
    protected function getProductListBlock()
    {
        return $this->objectManager->create(ProductListBlock::class);
    }

    /**
     * @magentoAppArea frontend
     */
    public function testPluginIsConfiguredToInterceptCallsInFrontendArea()
    {
        $plugins = $this->getCatalogProductListBlockPlugins();
        $this->assertSame(
            ListProductPlugin::class,
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
