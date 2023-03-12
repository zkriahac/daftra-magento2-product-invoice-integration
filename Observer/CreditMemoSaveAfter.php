<?php
namespace Websoft\Daftra\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CreditMemoSaveAfter implements ObserverInterface
{
    protected $helper;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Websoft\Daftra\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }


    public function execute(Observer $observer)
    {
        if(!$this->helper->isEnabled() || !$this->helper->isRefundSyncEnabled()){
            return;
        }
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $this->helper->sendRefund($creditmemo);

    }
}