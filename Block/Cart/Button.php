<?php
/**
 * @author Alex Kusakin
 */

namespace AlexKusakin\QuoteCsv\Block\Cart;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use AlexKusakin\QuoteCsv\Model\Config;

class Button extends Template
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * DownloadCsv constructor.
     * @param Context $context
     * @param Config $config
     * @param Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        Session $checkoutSession,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Is button enabled
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAvailable()
    {
        $storeId = $this->_storeManager->getStore()->getId();

        return $this->config->isEnabled($storeId)
            && $this->checkoutSession->getQuote()->hasItems();
    }

    /**
     * Get CSV file button URL
     *
     * @return string
     */
    public function getButtonUrl()
    {
        return $this->getUrl('quotecsv/cart/getCsv');
    }
}
