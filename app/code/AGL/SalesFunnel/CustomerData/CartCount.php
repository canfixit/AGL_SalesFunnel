<?php
namespace AGL\SalesFunnel\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\View\LayoutFactory;

class CartCount implements SectionSourceInterface
{
    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \AGL\SalesFunnel\Block\Product\CartCount
     */
    protected $block;

    public function __construct(LayoutFactory $layoutFactory)
    {
        $this->layoutFactory = $layoutFactory;
        $layout = $this->layoutFactory->create();
        $this->block = $layout->createBlock(\AGL\SalesFunnel\Block\Product\CartCount::class);
    }

    /**
     * @inheritdoc
     */
    public function getSectionData()
    {
        $count = 0;
        if ($this->block) {
            $count = (int)$this->block->getCartCount();
        }
        return ['count' => $count];
    }
}
