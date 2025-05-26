<?php
namespace AGL\SalesFunnel\Api;

interface AglProductSearchInterface
{
    /**
     * Get all AGL products with SKU and cart_count
     * @return array
     */
    public function getAglProducts();
} 