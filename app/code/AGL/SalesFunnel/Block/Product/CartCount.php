<?php
namespace AGL\SalesFunnel\Block\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Template;

class CartCount extends Template
{
    protected $resource;
    protected $product;

    public function __construct(
        Context $context,
        ResourceConnection $resource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->resource = $resource;
        $this->product = $context->getRegistry()->registry('current_product');
    }

    public function canShow()
    {
        return $this->product && $this->product->getData('agl_product');
    }

    public function getCartCount()
    {
        if (!$this->product) {
            return 0;
        }
        $sku = $this->product->getSku();
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('agl_salesfunnel_cart_count');
        $select = $connection->select()->from($table, ['cart_count'])->where('sku = ?', $sku);
        $row = $connection->fetchRow($select);
        return $row ? (int)$row['cart_count'] : 0;
    }
}
