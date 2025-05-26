<?php
namespace AGL\SalesFunnel\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use AGL\SalesFunnel\Model\AglProductSearch;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResourceConnection;

class AglProductSearchTest extends TestCase
{
    public function testGetAglProductsReturnsCorrectData()
    {
        $collectionFactory = $this->createMock(CollectionFactory::class);
        $collection = $this->getMockBuilder('Magento\\Catalog\\Model\\ResourceModel\\Product\\Collection')->disableOriginalConstructor()->getMock();
        $collectionFactory->method('create')->willReturn($collection);
        $collection->method('addAttributeToSelect')->willReturnSelf();
        $collection->method('addAttributeToFilter')->willReturnSelf();
        $collection->method('getColumnValues')->with('sku')->willReturn(['AGL1', 'AGL2']);

        $resource = $this->createMock(ResourceConnection::class);
        $connection = $this->getMockBuilder('Magento\\Framework\\DB\\Adapter\\AdapterInterface')->getMock();
        $resource->method('getConnection')->willReturn($connection);
        $resource->method('getTableName')->willReturn('agl_salesfunnel_cart_count');

        // Mock the select object (Zend_Db_Select)
        $select = $this->getMockBuilder('Zend_Db_Select')
            ->disableOriginalConstructor()
            ->getMock();
        $select->method('from')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $connection->method('select')->willReturn($select);
        $connection->method('fetchAll')->willReturn([
            ['sku' => 'AGL1', 'cart_count' => 5],
            ['sku' => 'AGL2', 'cart_count' => 2],
        ]);

        $model = new AglProductSearch($collectionFactory, $resource);
        $result = $model->getAglProducts();
        $this->assertEquals([
            ['sku' => 'AGL1', 'cart_count' => 5],
            ['sku' => 'AGL2', 'cart_count' => 2],
        ], $result);
    }
} 