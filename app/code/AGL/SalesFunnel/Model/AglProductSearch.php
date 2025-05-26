<?php
namespace AGL\SalesFunnel\Model;

use AGL\SalesFunnel\Api\AglProductSearchInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResourceConnection;

class AglProductSearch implements AglProductSearchInterface
{
    protected $productCollectionFactory;
    protected $resource;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        ResourceConnection $resource
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resource = $resource;
    }

    public function getAglProducts()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['sku', 'agl_product']);
        $collection->addAttributeToFilter('agl_product', 1);
        $skus = $collection->getColumnValues('sku');
        $result = [];
        if ($skus) {
            $connection = $this->resource->getConnection();
            $table = $this->resource->getTableName('agl_salesfunnel_cart_count');
            $select = $connection->select()->from($table, ['sku', 'cart_count'])->where('sku IN (?)', $skus);
            $rows = $connection->fetchAll($select);
            $cartCounts = [];
            foreach ($rows as $row) {
                $cartCounts[$row['sku']] = (int)$row['cart_count'];
            }
            foreach ($skus as $sku) {
                $result[] = [
                    'sku' => $sku,
                    'cart_count' => isset($cartCounts[$sku]) ? $cartCounts[$sku] : 0
                ];
            }
        }
        return $result;
    }
} 