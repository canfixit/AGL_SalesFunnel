<?php
namespace AGL\SalesFunnel\Test\Unit\Plugin\Api;

use PHPUnit\Framework\TestCase;
use AGL\SalesFunnel\Plugin\Api\ProductRepositoryPlugin;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\App\ResourceConnection;

class ProductRepositoryPluginTest extends TestCase
{
    public function testAfterGetListSetsCustomAttributes()
    {
        $resource = $this->createMock(ResourceConnection::class);
        $connection = $this->getMockBuilder('Magento\\Framework\\DB\\Adapter\\AdapterInterface')->getMock();
        $resource->method('getConnection')->willReturn($connection);
        $resource->method('getTableName')->willReturn('agl_salesfunnel_cart_count');

        $productAgl = $this->getMockBuilder('Magento\\Catalog\\Model\\Product')->disableOriginalConstructor()->getMock();
        $productAgl->method('getCustomAttribute')->with('agl_product')->willReturn(new class {
            public function getValue() { return 1; }
        });
        $productAgl->method('getSku')->willReturn('AGL1');
        $callArgs = [];
        $productAgl->method('setCustomAttribute')
            ->willReturnCallback(function($name, $value) use (&$callArgs, $productAgl) {
                $callArgs[] = [$name, $value];
                return $productAgl;
            });

        $productNonAgl = $this->getMockBuilder('Magento\\Catalog\\Model\\Product')->disableOriginalConstructor()->getMock();
        $productNonAgl->method('getCustomAttribute')->with('agl_product')->willReturn(null);
        $productNonAgl->method('getSku')->willReturn('NONAGL1');
        $productNonAgl->expects($this->once())->method('setCustomAttribute')->with('agl_product', false)->willReturnSelf();

        $connection->method('select')->willReturnSelf();
        $connection->method('from')->willReturnSelf();
        $connection->method('where')->willReturnSelf();
        $connection->method('fetchRow')->willReturn(['cart_count' => 5]);

        $searchResults = $this->createMock(SearchResultsInterface::class);
        $searchResults->method('getItems')->willReturn([$productAgl, $productNonAgl]);

        $plugin = new ProductRepositoryPlugin($resource);
        $result = $plugin->afterGetList($this->createMock(ProductRepositoryInterface::class), $searchResults);
        $this->assertSame($searchResults, $result);
        $this->assertCount(2, $callArgs);
        $this->assertSame(['agl_product', true], $callArgs[0]);
        $this->assertSame('cart_count', $callArgs[1][0]);
        $this->assertStringContainsString('AGL customers', $callArgs[1][1]);
    }
} 