<?php

declare(strict_types=1);

namespace MageSuite\CategoryHero\Test\Integration\Plugin\Block\Catalog\Product;

class ListProductPluginTest extends \PHPUnit\Framework\TestCase
{
    protected string $pluginName = 'category_hero_product_list_plugin';

    protected string $categoryRegistryKey = 'current_category';

    protected \Magento\TestFramework\ObjectManager $objectManager;

    protected \Magento\Framework\Registry $registry;

    protected \Magento\Catalog\Model\Category $categoryModel;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->categoryModel = $this->objectManager->create(\Magento\Catalog\Model\Category::class);
        $this->registry->unregister($this->categoryRegistryKey);
    }

    protected function getCatalogProductListBlockPlugins(): array
    {
        /** @var \Magento\TestFramework\Interception\PluginList $pluginList */
        $pluginList = $this->objectManager->get(\Magento\TestFramework\Interception\PluginList::class);
        return $pluginList->get(\Magento\Catalog\Block\Product\ListProduct::class, []);
    }

    protected function loadAndRegisterCategory(int $categoryId): void
    {
        $category = $this->objectManager
            ->create(\Magento\Catalog\Model\Category::class)
            ->load($categoryId);
        $this->registry->unregister($this->categoryRegistryKey);
        $this->registry->register($this->categoryRegistryKey, $category);
    }

    protected function getProductListBlock(): \Magento\Catalog\Block\Product\ListProduct
    {
        return $this->objectManager->create(\Magento\Catalog\Block\Product\ListProduct::class);
    }

    /**
     * @magentoAppArea frontend
     */
    public function testPluginIsConfiguredToInterceptCallsInFrontendArea(): void
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
    public function testPluginIsConfiguredNotToInterceptCallsInAdminhtmlArea(): void
    {
        $plugins = $this->getCatalogProductListBlockPlugins();
        $this->assertArrayNotHasKey($this->pluginName, $plugins);
    }

    /**
     * @magentoDataFixture MageSuite_CategoryHero::Test/Integration/_files/categories_no_products.php
     * @magentoAppArea frontend
     * @dataProvider categoryProvider
     */
    public function testPluginReturnsValueOfEnableHeroProductAttributeWhenCategoryIsRegistered(int $categoryId, bool $heroEnabled): void
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
    public function testPluginReturnsFalseWhenNoCategoryIsRegistered(): void
    {
        $this->assertFalse($this->getProductListBlock()->getIsHeroEnabled());
    }

    public static function categoryProvider(): array
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
}
