<?php
namespace AGL\SalesFunnel\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Product;

class CartProductAddAfter implements ObserverInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    public function execute(Observer $observer)
    {
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();
        if (!$product->getId() || !$product->getData('agl_product')) {
            return;
        }
        $sku = $product->getSku();
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('agl_salesfunnel_cart_count');
        $select = $connection->select()->from($table)->where('sku = ?', $sku);
        $row = $connection->fetchRow($select);
        if ($row) {
            $connection->update($table, ['cart_count' => $row['cart_count'] + 1], ['sku = ?' => $sku]);
        } else {
            $connection->insert($table, ['sku' => $sku, 'cart_count' => 1]);
        }
    }
} 