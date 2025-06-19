<?php
namespace AGL\SalesFunnel\Plugin\Shipping;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\App\ResourceConnection;
use \Psr\Log\LoggerInterface;


class FlatratePlugin
{
    protected $resource;
    protected $logger;
    public function __construct(ResourceConnection $resource, LoggerInterface $logger)
    {
        $this->resource = $resource;
        $this->logger = $logger;
    }

    public function afterCollectRates($subject, $result, RateRequest $request)
    {
        if (!$result || !isset($result->getAllRates()[0])) {
            return $result;
        }
        $items = $request->getAllItems();
        $hasAglProduct = false;
        foreach ($items as $item) {
            $product = $item->getProduct();
            if ($product && $product->getData('agl_product')) {
                $hasAglProduct = true;
                break;
            }
        }
        if ($hasAglProduct) {
            foreach ($result->getAllRates() as $rate) {
                if ($rate->getCarrier() === 'flatrate') {
                    $rate->setPrice(10);
                    $rate->setCost(10);
                }
            }
        }
        return $result;
    }
}
