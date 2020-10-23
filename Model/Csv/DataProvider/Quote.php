<?php
/**
 * @author Alex Kusakin
 */

namespace AlexKusakin\QuoteCsv\Model\Csv\DataProvider;


use Magento\Quote\Model\Quote\Item;

/**
 * CSV quote data provider
 */
class Quote
{
    const FIELD_SKU = 'SKU';
    const FIELD_NAME = 'Name';
    const FIELD_QUANTITY = 'Quantity';
    const FIELD_PRICE = 'Total Price';

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Quote constructor.
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function __construct(
        \Magento\Quote\Model\Quote $quote
    ) {
        $this->quote = $quote;
    }

    /**
     * Get all quote items data
     *
     * @return array
     */
    public function getData()
    {
        $data = [];
        foreach ($this->quote->getAllVisibleItems() as $item) {
            $data[] = $this->getItemData($item);
        }

        return $data;
    }

    /**
     * Get CSV file headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            self::FIELD_SKU,
            self::FIELD_NAME,
            self::FIELD_QUANTITY,
            self::FIELD_PRICE
        ];
    }

    /**
     * Get quote item data
     *
     * @param Item $item
     * @return array
     */
    public function getItemData(Item $item)
    {
        return [
            $item->getSku(),
            $item->getName(),
            $item->getQty(),
            $item->getRowTotalInclTax()
        ];
    }
}
