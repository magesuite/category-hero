<?php
/**
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\CategoryHero\Test\Integration;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\ObjectManager;

class ExtensionSetupTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $moduleName = 'MageSuite_CategoryHero';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    public function testCategoryHeroIsRegisteredAsModule()
    {
        /** @var ComponentRegistrar $componentRegistrar */
        $componentRegistrar = new ComponentRegistrar();
        $this->assertArrayHasKey(
            $this->moduleName,
            $componentRegistrar->getPaths(ComponentRegistrar::MODULE)
        );
    }

    public function testCategoryHeroIsEnabled()
    {
        /** @var ModuleList $moduleList */
        $moduleList = $this->objectManager->get(ModuleList::class);
        $this->assertTrue($moduleList->has($this->moduleName));
    }
}
