<?php

/**
 * Magestore.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Affiliateplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Affiliateplus\Block\Plugin\ReferFriend\Product;
class RelatedListProduct{
    /**
     * @var \Magestore\Affiliateplus\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magestore\Affiliateplus\Helper\Account
     */
    protected $_accountHelper;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * ListProduct constructor.
     * @param \Magestore\Affiliateplus\Helper\Data $dataHelper
     * @param \Magestore\Affiliateplus\Helper\Account $accountHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magestore\Affiliateplus\Helper\Data $dataHelper,
        \Magestore\Affiliateplus\Helper\Account $accountHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_dataHelper = $dataHelper;
        $this->_accountHelper = $accountHelper;
        $this->_objectManager = $objectManager;
    }

    /**
     * @param \Magento\Catalog\Block\Product\AbstractProduct $listProduct
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @param $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function aroundGetProductPriceHtml(
        \Magento\Catalog\Block\Product\AbstractProduct $listProduct,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product,
        $priceType,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ){

        $price = $proceed($product, $priceType, $renderZone, $arguments);

        if (!$this->_dataHelper->isAffiliateModuleEnabled()
            || !$this->_dataHelper->getConfig('affiliateplus/refer/enable')
            || $this->_accountHelper->accountNotLogin()
            || !$this->_dataHelper->getConfig('affiliateplus/refer/refer_enable_product_detail')
        ) {
            return $price;
        }

        $block = $this->_objectManager->create('Magestore\Affiliateplus\Block\ReferFriend\Product\Refer');
        $block->setCurrentProduct($product);
        return $price.$block->toHtml();
    }
}