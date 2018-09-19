<?php
/**
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\CategoryHero\Test\Integration\Setup;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Shell;
use Magento\Setup\Model\InstallerFactory;
use Magento\TestFramework\ObjectManager;

class InstallDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $attributeCode = 'enable_hero_product';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var InstallerFactory
     */
    protected $installerFactory;

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var array|null
     */
    protected $appDirs = null;

    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
        $this->shell = $this->objectManager->get(Shell::class);
        $this->directoryList = $this->objectManager->get(DirectoryList::class);
    }

    /**
     * Gets sandbox app directory paths
     *
     * @return array
     */
    protected function getAppDirs()
    {
        if (null === $this->appDirs) {
            $paths = [
                DirectoryList::CONFIG,
                DirectoryList::VAR_DIR,
                DirectoryList::MEDIA,
                DirectoryList::STATIC_VIEW,
                DirectoryList::GENERATION,
                DirectoryList::CACHE,
                DirectoryList::LOG,
                DirectoryList::SESSION,
                DirectoryList::TMP,
                DirectoryList::UPLOAD,
                DirectoryList::PUB,
            ];
            $this->appDirs = array_combine($paths, array_map(function ($pathCode) {
                return [DirectoryList::PATH => $this->directoryList->getPath($pathCode)];
            }, $paths));
        }
        return $this->appDirs;
    }

    /**
     * @return string|null
     */
    protected function getDeployMode()
    {
        return defined('TESTS_MAGENTO_MODE') ? TESTS_MAGENTO_MODE : null;
    }

    /**
     * @return string
     */
    protected function getInitParamsQuery()
    {
        $initParams = [
            \Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS => $this->getAppDirs(),
            \Magento\Framework\App\State::PARAM_MODE => $this->getDeployMode()
        ];
        return urldecode(http_build_query($initParams));
    }

    protected function runSetupUpgradeShellCommand()
    {
        $params = ['--magento-init-params=%s' => $this->getInitParamsQuery()];
        $this->shell->execute(
            PHP_BINARY . ' -f %s setup:upgrade -vvv ' . implode(' ', array_keys($params)),
            array_merge([BP . '/bin/magento'], array_values($params))
        );
    }

    public function testEnableHeroProductAttributeForCategoryIsInstalled()
    {
        $this->markTestSkipped('Skip due to database unreliable cleanup procedure');
//        $this->runSetupUpgradeShellCommand();
        /** @var EavConfig $eavConfig */
        $eavConfig = $this->objectManager->get(EavConfig::class);
        $attribute = $eavConfig->getAttribute(Category::ENTITY, $this->attributeCode);
        $this->assertInternalType(
            \PHPUnit_Framework_Constraint_IsType::TYPE_INT,
            filter_var($attribute->getAttributeId(), FILTER_VALIDATE_INT)
        );
    }
}
