<?php
/**
 * @author Alex Kusakin
 */
namespace AlexKusakin\QuoteCsv\Test\Unit\Model\Csv\DataProvider;

class QuoteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    protected $mockItemsArray = [
        ['skuA', 'Test A', 3, 12.6],
        ['skuB', 'Test B', 1, 13.25],
        ['skuC', 'Test C', 5, 20]
    ];

    /**
     * Test getHeaders method
     */
    public function testGetHeaders()
    {
        // prepare mocks
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);

        // execute logic
        $dataProvider = new \AlexKusakin\QuoteCsv\Model\Csv\DataProvider\Quote($quoteMock);
        $result = $dataProvider->getHeaders();

        // verify results
        $this->assertEquals(['SKU', 'Name', 'Quantity', 'Total Price'], $result);

    }

    /**
     * Test getData method
     */
    public function testGetData()
    {
        // prepare mocks
        $mockItems = [];
        foreach ($this->mockItemsArray as $row) {
            $itemMock =  $this->createPartialMock(\Magento\Quote\Model\Quote\Item::class, []);
            $itemMock->setData([
                \Magento\Quote\Model\Quote\Item::KEY_SKU => $row[0],
                \Magento\Quote\Model\Quote\Item::KEY_NAME => $row[1],
                \Magento\Quote\Model\Quote\Item::KEY_QTY => $row[2],
                'row_total_incl_tax' => $row[3],
            ]);
            $mockItems[] = $itemMock;
        }

        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quoteMock->expects($this->any())
            ->method('getAllVisibleItems')
            ->willReturn($mockItems);

        // execute logic
        $dataProvider = new \AlexKusakin\QuoteCsv\Model\Csv\DataProvider\Quote($quoteMock);
        $result = $dataProvider->getData();

        // verify results
        $this->assertEquals($this->mockItemsArray, $result);
    }
}
