<?php
namespace AGL\SalesFunnel\Test\Unit\Plugin\Shipping;

use PHPUnit\Framework\TestCase;
use AGL\SalesFunnel\Plugin\Shipping\FlatratePlugin;
use Magento\Framework\App\ResourceConnection;
use Magento\Quote\Model\Quote\Address\RateRequest;

class FlatratePluginTest extends TestCase
{
    public function testAfterCollectRatesSetsPriceIfAglProductPresent()
    {
        $resource = $this->createMock(ResourceConnection::class);
        $connection = $this->getMockBuilder('Magento\\Framework\\DB\\Adapter\\AdapterInterface')->getMock();
        $resource->method('getConnection')->willReturn($connection);
        $resource->method('getTableName')->willReturnMap([
            ['catalog_product_entity', 'catalog_product_entity'],
            ['catalog_product_entity_int', 'catalog_product_entity_int'],
            ['eav_attribute', 'eav_attribute'],
        ]);
        $select = $this->getMockBuilder('Zend_Db_Select')
            ->disableOriginalConstructor()
            ->getMock();
        $select->method('from')->willReturnSelf();
        $select->method('join')->willReturnSelf();
        $select->method('where')->willReturnSelf();
        $connection->method('select')->willReturn($select);
        $connection->method('fetchOne')->willReturn(1);

        $item = $this->getMockBuilder('Magento\\Quote\\Model\\Quote\\Item')->disableOriginalConstructor()->getMock();
        $item->method('getSku')->willReturn('AGL1');
        $request = $this->createMock(RateRequest::class);
        $request->method('getAllItems')->willReturn([$item]);

        $rate = $this->getMockBuilder('Magento\\Quote\\Model\\Quote\\Address\\Rate')->disableOriginalConstructor()->getMock();
        $rate->method('getCode')->willReturn('flatrate_flatrate');
        $rate->expects($this->once())->method('setPrice')->with(10);
        $rate->expects($this->once())->method('setCost')->with(10);
        $result = $this->getMockBuilder('Magento\\Shipping\\Model\\Rate\Result')->disableOriginalConstructor()->getMock();
        $result->method('getAllRates')->willReturn([$rate]);

        $plugin = new FlatratePlugin($resource);
        $plugin->afterCollectRates($this->getMockBuilder('Magento\\Shipping\\Model\\Carrier\\Flatrate')->disableOriginalConstructor()->getMock(), $result, $request);
    }

    public function testAfterCollectRatesNoAglProductNoChange()
    {
        $resource = $this->createMock(ResourceConnection::class);
        $connection = $this->getMockBuilder('Magento\\Framework\\DB\\Adapter\\AdapterInterface')->getMock();
        $resource->method('getConnection')->willReturn($connection);
        $resource->method('getTableName')->willReturnMap([
            ['catalog_product_entity', 'catalog_product_entity'],
            ['catalog_product_entity_int', 'catalog_product_entity_int'],
            ['eav_attribute', 'eav_attribute'],
        ]);
        $select = $this->getMockBuilder('Zend_Db_Select')
            ->disableOriginalConstructor()
            ->getMock();
        $select->method('from')->willReturnSelf();
        $select->method('join')->willReturnSelf();
        $select->method('where')->willReturnSelf();
        $connection->method('select')->willReturn($select);
        $connection->method('fetchOne')->willReturn(false);

        $item = $this->getMockBuilder('Magento\\Quote\\Model\\Quote\\Item')->disableOriginalConstructor()->getMock();
        $item->method('getSku')->willReturn('NONAGL1');
        $request = $this->createMock(RateRequest::class);
        $request->method('getAllItems')->willReturn([$item]);

        $rate = $this->getMockBuilder('Magento\\Quote\\Model\\Quote\\Address\\Rate')->disableOriginalConstructor()->getMock();
        $rate->method('getCode')->willReturn('flatrate_flatrate');
        $rate->expects($this->never())->method('setPrice');
        $rate->expects($this->never())->method('setCost');
        $result = $this->getMockBuilder('Magento\\Shipping\\Model\\Rate\Result')->disableOriginalConstructor()->getMock();
        $result->method('getAllRates')->willReturn([$rate]);

        $plugin = new FlatratePlugin($resource);
        $plugin->afterCollectRates($this->getMockBuilder('Magento\\Shipping\\Model\\Carrier\\Flatrate')->disableOriginalConstructor()->getMock(), $result, $request);
    }
} 