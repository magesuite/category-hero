<?php

namespace MageSuite\CategoryHero\Test\Integration;


class ExtensionSetupTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $moduleName = 'MageSuite_CategoryHero';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
    }

    public function testCategoryHeroIsRegisteredAsModule()
    {
        /** @var \Magento\Framework\Component\ComponentRegistrar $componentRegistrar */
        $componentRegistrar = new \Magento\Framework\Component\ComponentRegistrar();
        $this->assertArrayHasKey(
            $this->moduleName,
            $componentRegistrar->getPaths(\Magento\Framework\Component\ComponentRegistrar::MODULE)
        );
    }

    public function testCategoryHeroIsEnabled()
    {
        /** @var \Magento\Framework\Module\ModuleList $moduleList */
        $moduleList = $this->objectManager->get(\Magento\Framework\Module\ModuleList::class);
        $this->assertTrue($moduleList->has($this->moduleName));
    }
}
