<?php

namespace MageSuite\CategoryHero\Test\Unit\Plugin\Block\Catalog\Product;

class ListProductPluginTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\CategoryHero\Plugin\Block\Catalog\Product\ListProductPlugin
     */
    protected $pluginInstance;

    protected function setUp(): void
    {
        $registryStub = $this->getMockBuilder(\Magento\Framework\Registry::class)
            ->getMock();

        $this->pluginInstance = new \MageSuite\CategoryHero\Plugin\Block\Catalog\Product\ListProductPlugin($registryStub);
    }

    public function testPluginCanBeInstantiated()
    {
        $this->assertInstanceOf(\MageSuite\CategoryHero\Plugin\Block\Catalog\Product\ListProductPlugin::class, $this->pluginInstance);
    }
}
