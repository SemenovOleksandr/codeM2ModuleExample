<?php

namespace VendorName\HelloWorld\Model\Rule\Discount\Calculator\ByFixedPrice;

use VendorName\Helloworld\Model\Rule\Discount\Calculator\ByFixedPrice\Items as ItemsHelloWorld;

/**
 * Class Items
 * @package Vendor\Helloworld\Model\Rule\Discount\Calculator\ByFixedPrice
 */
class Items extends ItemsHelloWorld
{
    /**
     * Retrieve rule percent
     *
     * @param RuleMetadataInterface $metadataRule
     * @param float $price
     * @return int|float
     */
    private function getRulePercent($metadataRule, $price)
    {
        $quoteItem = $metadataRule->getQuoteItem();
        $discountAmount = $metadataRule->getRule()->getDiscountAmount();

        if ($quoteItem) {
            $key = $quoteItem->getId() . '_' . $metadataRule->getRule()->getRuleId();
            if (!isset($this->percents[$key])) {
                $percent = ($quoteItem->getBasePrice() - $discountAmount) / $quoteItem->getBasePrice() * 100;
                $this->percents[$key] = min(100, $percent);
            }
            return $this->percents[$key];
        }
        try {
            /** @var Store $store */
            $store = $this->storeManager->getStore();
            $discountAmount = $store->getCurrentCurrencyRate() * $discountAmount;
        } catch (NoSuchEntityException $e) {
        }

        $percent = $price == 0 ? 0 : (($price - $discountAmount) / $price * 100);

        return min(100, $percent);
    }
}	
