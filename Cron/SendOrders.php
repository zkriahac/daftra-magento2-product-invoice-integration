<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Cron;

class SendOrders
{

    protected $logger;
    protected $orderCollectionFactory;
    protected $helper;
    protected $orderModel;
    protected $store;
    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Websoft\Daftra\Helper\Data $helper,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Store\Model\StoreManagerInterface $store
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->orderModel = $orderModel;
        $this->store = $store;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        if(!$this->helper->isEnabled() || !$this->helper->isOrderSyncEnabled()){
            return;
        }
        $collection = $this->getOrderCollection();
        $this->sendOrders($collection);
        $this->logger->info("Cronjob SendOrders is executed." . count($collection));
    }

    public function getOrderCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        $from = $objDate->timestamp('2022-08-19');
        $from = date('Y-m-d' . ' 00:00:00', $from);

        $collection = $this->orderCollectionFactory->create()
          ->addAttributeToSelect('*')
          ->addFieldToFilter('created_at', ['from' => $from])
          ->addFieldToFilter('invoice_id', ['null' => true])
          ->addFieldToFilter('status',['in'=>['complete']])
          ->addFieldToFilter('state',['in'=>['complete']])
          ->setPageSize(10);
        
        return $collection;
    }

    private function sendOrders($collection)
    {
        foreach($collection as $order){
            $this->helper->sendInvoices($order);
        }
    }


}