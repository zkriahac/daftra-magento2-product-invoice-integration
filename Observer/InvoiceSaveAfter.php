<?php

namespace Websoft\Daftra\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class InvoiceSaveAfter
 * @package Ced\Booking\Observer
 */
class InvoiceSaveAfter implements ObserverInterface
{
    protected $helper;
    public function __construct(
        \Websoft\Daftra\Helper\Data $helper
        )
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        if(!$this->helper->isEnabled()){
            return $this;
        }

        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $this->helper->sendInvoices($order, 'invoice');
        return $this;
    }
}



