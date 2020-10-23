<?php
/**
 * @author Alex Kusakin
 */

namespace AlexKusakin\QuoteCsv\Controller\Cart;

use AlexKusakin\QuoteCsv\Model\Config;
use AlexKusakin\QuoteCsv\Model\Csv\Converter;
use Exception;
use Magento\Catalog\Controller\Product\View\ViewInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;

class GetCsv extends Action implements ViewInterface
{
    const RESPONSE_FILE_NAME = 'cart.csv';

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var Config
     */
    protected $config;

    /**
     * GetCsv action constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param FileFactory $fileFactory
     * @param Converter $converter
     * @param Config $config
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        FileFactory $fileFactory,
        Converter $converter,
        Config $config
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->fileFactory = $fileFactory;
        $this->converter = $converter;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->config->isEnabled() || !$this->getQuote()->hasItems()) {
            return $this->redirectToCart();
        }

        return $this->fileFactory->create(
            self::RESPONSE_FILE_NAME,
            $this->converter->getCsvFile($this->getQuote()),
            'var'
        );
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function redirectToCart()
    {
        return $this->resultRedirectFactory->create()
            ->setPath('checkout/cart');
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }
}
