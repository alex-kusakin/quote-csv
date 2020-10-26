<?php
/**
 * @author Alex Kusakin
 */
namespace AlexKusakin\QuoteCsv\Test\Unit\Model\Csv;

class ConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    protected $csvArray = [
        ['SKU','Name','Quantity','Total Price'],
        ['skuA','Test A',3,12.6],
        ['skuB','Test B',1,13.25],
        ['skuC','Test C',5,20]
    ];

    /**
     * Test getCsvFile method
     */
    public function testGetCsvFile()
    {
        // prepare mocks
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quoteMock->expects($this->any())
            ->method('getId')
            ->willReturn(1234);

        // execute logic
        $converter = new \AlexKusakin\QuoteCsv\Model\Csv\Converter(
            $this->getFileSystemMock(),
            $this->getDataProviderMock()
        );

        $result = $converter->getCsvFile($quoteMock);

        // verify results
        $this->assertTrue(is_array($result));
        $this->assertTrue($result['rm']);
        $this->assertEquals('filename', $result['type']);
        $this->assertTrue(strpos($result['value'], 'export/quote_1234_') === 0);

    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getFileSystemMock()
    {
        $streamMock = $this->createMock(\Magento\Framework\Filesystem\File\WriteInterface::class);
        $streamMock->expects($this->exactly(4))
            ->method('writeCsv')
            ->withConsecutive(
                [$this->equalTo($this->csvArray[0])],
                [$this->equalTo($this->csvArray[1])],
                [$this->equalTo($this->csvArray[2])],
                [$this->equalTo($this->csvArray[3])]
            );

        $directoryMock = $this->createMock(\Magento\Framework\Filesystem\Directory\WriteInterface::class);
        $directoryMock->expects($this->once())
            ->method('openFile')
            ->willReturn($streamMock);

        $filesystemMock = $this->createMock(\Magento\Framework\Filesystem::class);
        $filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
            ->willReturn($directoryMock);

        return $filesystemMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getDataProviderMock()
    {
        $dataProviderMock = $this->createMock(\AlexKusakin\QuoteCsv\Model\Csv\DataProvider\Quote::class);
        $dataProviderMock->expects($this->any())
            ->method('getData')
            ->willReturn([
                $this->csvArray[1],
                $this->csvArray[2],
                $this->csvArray[3]
            ]);

        $dataProviderMock->expects($this->any())
            ->method('getHeaders')
            ->willReturn($this->csvArray[0]);


        $dataProviderFactoryMock = $this->createMock('\AlexKusakin\QuoteCsv\Model\Csv\DataProvider\QuoteFactory');
        $dataProviderFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($dataProviderMock);

        return $dataProviderFactoryMock;
    }
}
