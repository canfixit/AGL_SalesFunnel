<?php
namespace AGL\SalesFunnel\Plugin\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Api\Data\ProductExtensionFactory;

class ProductRepositoryPlugin
{
    protected $resource;
    protected $extensionFactory;

    public function __construct(ResourceConnection $resource, ProductExtensionFactory $extensionFactory)
    {
        $this->resource = $resource;
        $this->extensionFactory = $extensionFactory;
    }

    public function afterGetList(ProductRepositoryInterface $subject, SearchResultsInterface $result)
    {
        $items = $result->getItems();
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('agl_salesfunnel_cart_count');
        foreach ($items as $product) {
            if ($product->getCustomAttribute('agl_product')) {
                $isAgl = (bool)$product->getCustomAttribute('agl_product')->getValue();
            } else {
                $isAgl = false;
            }
            $sku = $product->getSku();
            $cartCount = 0;
            if ($isAgl) {
                $select = $connection->select()->from($table, ['cart_count'])->where('sku = ?', $sku);
                $row = $connection->fetchRow($select);
                $cartCount = $row ? (int)$row['cart_count'] : 0;
            }
            $product->setCustomAttribute('agl_product', $isAgl);
            // Set cart_count as extension attribute
            $extensionAttributes = $product->getExtensionAttributes();
            if (!$extensionAttributes) {
                $extensionAttributes = $this->extensionFactory->create();
            }
            $extensionAttributes->setCartCount($isAgl ? $cartCount : null);
            $product->setExtensionAttributes($extensionAttributes);
        }
        return $result;
    }
} 