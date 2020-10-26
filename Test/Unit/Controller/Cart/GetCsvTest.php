<?php
/**
 * @author Alex Kusakin
 */
namespace AlexKusakin\QuoteCsv\Test\Unit\Controller\Cart;

class GetCsvTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test controller execute method
     *
     * @dataProvider controllerProvider
     *
     * @param $quoteHasItems - condition - has quote items?
     * @param $configIsEnabled - condition - is config enabled?
     * @param $getCsvThrowsException - condition - error is thrown by logic
     * @param $expectConvertCsv - result - csv conversion logic is triggered
     * @param $returnRedirect - result - is redirect response expected
     */
    public function testExecuteProvider(
        $quoteHasItems, $configIsEnabled, $getCsvThrowsException, $expectConvertCsv, $returnRedirect
    ) {
        // prepare mocks
        $redirectMock = $this->createMock(\Magento\Framework\Controller\Result\Redirect::class);
        $redirectMock->expects($this->any())
            ->method('setPath')
            ->willReturnSelf();

        $redirectFactoryMock = $this->createMock(\Magento\Framework\Controller\Result\RedirectFactory::class);
        $redirectFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($redirectMock);

        $contextMock = $this->createMock(\Magento\Framework\App\Action\Context::class);
        $contextMock->expects($this->any())
            ->method('getResultRedirectFactory')
            ->willReturn($redirectFactoryMock);

        $contextMock->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->createMock(\Magento\Framework\Message\ManagerInterface::class));


        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quoteMock->expects($this->any())
            ->method('hasItems')
            ->willReturn($quoteHasItems);

        $checkoutSessionMock = $this->createMock(\Magento\Checkout\Model\Session::class);
        $checkoutSessionMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $responseMock = $this->createMock(\Magento\Framework\App\ResponseInterface::class);
        $fileFactoryMock = $this->createMock(\Magento\Framework\App\Response\Http\FileFactory::class);
        $fileFactoryMock->expects($this->any())
            ->method('create')
            ->with('cart.csv', ['test csv response'], 'var')
            ->willReturn($responseMock);

        $converterMock = $this->createMock(\AlexKusakin\QuoteCsv\Model\Csv\Converter::class);
        $converterMock->expects($expectConvertCsv ? $this->once() : $this->never())
            ->method('getCsvFile')
            ->with($quoteMock)
            ->will($getCsvThrowsException
                ? $this->throwException(new \Exception('Test exception.'))
                : $this->returnValue(['test csv response'])
            );

        $configMock = $this->createMock(\AlexKusakin\QuoteCsv\Model\Config::class);
        $configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn($configIsEnabled);

        // execute test
        $controller = new \AlexKusakin\QuoteCsv\Controller\Cart\GetCsv(
            $contextMock, $checkoutSessionMock, $fileFactoryMock, $converterMock, $configMock
        );

        $result = $controller->execute();

        // verify results
        $expectedType = $returnRedirect
            ? \Magento\Framework\Controller\Result\Redirect::class
            : \Magento\Framework\App\ResponseInterface::class;

        $this->assertInstanceOf($expectedType, $result);

    }

    /**
     * @return array
     */
    public function controllerProvider()
    {
        return [
            // feature is disabled
            [true, false, false, false, true],
            // cart is empty
            [false, true, false, false, true],
            // both feature is disabled and cart is empty
            [false, false, false, false, true],
            // success test - csv converting is triggered
            [true, true, false, true, false],
            // error - casv converting thrown an exception
            [true, true, true, true, true],
        ];
    }
}
