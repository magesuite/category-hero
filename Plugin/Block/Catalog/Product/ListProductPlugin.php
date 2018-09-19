<?php
/**
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\CategoryHero\Plugin\Block\Catalog\Product;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Framework\Registry;

class ListProductPlugin
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\Category|null
     */
    protected $currentCategory = null;

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @return \Magento\Catalog\Model\Category|null
     */
    protected function getCurrentCategory()
    {
        if (null === $this->currentCategory) {
            $this->currentCategory = $this->registry->registry('current_category');
        }
        return $this->currentCategory;
    }

    /**
     * @param ListProduct $subject
     * @param \Closure $proceed
     * @param string $key
     * @param string|int $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetData(ListProduct $subject, \Closure $proceed, $key = '', $index = null)
    {
        if ($key === 'is_hero_enabled') {
            if ($currentCategory = $this->getCurrentCategory()) {
                return (bool)$currentCategory->getData('enable_hero_product');
            }
            return false;
        }
        return $proceed($key, $index);
    }
}
