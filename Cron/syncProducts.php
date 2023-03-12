<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Cron;

class syncProducts
{

    protected $debug = 0;

    protected $_logger;
    protected $_productCollectionFactory;
    protected $_helper;
    protected $store;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Websoft\Daftra\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $store
    ){
        $this->_logger = $logger;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_helper = $helper;
        $this->store = $store;

    }


    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        if(!$this->_helper->isEnabled() || !$this->_helper->isProductSyncEnabled()){
            return;
        }

        $collection = $this->getProductCollection();
        $skus=[];
        foreach($collection as $product){
            $sku = $product->getSku();
            $this->_helper->syncProduct($product->getSku());
            $skus[] = $sku;
        }
        $this->_logger->info("Cronjob syncProducts is executed.".json_encode($skus));
    }


    public function getProductCollection()
    {
        // Filter datetime
        $collection = $this->_productCollectionFactory->create()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('status', array('eq' => 1) )
        ->addAttributeToFilter('visibility', array('in' => [2, 3, 4]) )
        ->addFieldToFilter('daftra_id', ['null' => true])
        ->addStoreFilter(0)  // here to specify the store view to send products names    
        ->setPageSize(10);
        return $collection;
    }
}

