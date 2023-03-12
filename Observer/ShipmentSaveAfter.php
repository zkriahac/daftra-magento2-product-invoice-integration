<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Websoft\Daftra\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class OrderCreditmemoRefund
 * @package Ced\CsMarketplace\Observer
 */
Class ShipmentSaveAfter implements ObserverInterface
{

    protected $helper;
    public function __construct(
        \Websoft\Daftra\Helper\Data $helper
        )
    {
        $this->helper = $helper;
    }

    /**
     * Refund the associated vendor order
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if(!$this->helper->isEnabled()){
            return $this;
        }

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $this->helper->sendInvoices($order,'shipment');

        return $this;
    }
}    
