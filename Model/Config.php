<?php
/**
 * @author Alex Kusakin
 */

namespace AlexKusakin\QuoteCsv\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Module configurations
 * @package AlexKusakin\QuoteCsv\Block\Cart
 */
class Config
{
    const XML_PATH_CSV_ENABLED = 'shopping_cart/csv/enabled';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * IS feature enabled
     *
     * @param null|int $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CSV_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
