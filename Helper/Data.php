<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Websoft\Daftra\Model\GuestClientFactory;
use Websoft\Daftra\Model\ResourceModel\GuestClient\CollectionFactory;
use Magento\Customer\Api\AccountManagementInterface;

class Data extends AbstractHelper 
{
	const MODULE_ENABLED = 'daftara/general/enabled';
	const ORDER_SYNC_ENABLED = 'daftara/general/order_sync_enabled';
    const REFUND_SYNC_ENABLED = 'daftara/general/refund_sync_enabled';
	const PRODUCT_SYNC_ENABLED = 'daftara/general/product_sync_enabled';
	const ACCESS_TOKEN = 'daftara/general/token';
    const SEND_TAX = 'daftara/general/send_tax';
    const TAX_ID = 'daftara/general/tax_id';
    const FEE = 'daftara/general/payment_fee_id';
    const DEBUG_ENABLED = 'daftara/general/debug_enabled';
     
    const GET = '0';
	const POST = '1';
	const PUT = '2';

    protected $scopeConfig;

    protected $logger;

    protected $orderModel;

    protected $customerModel;

    protected $timezone;
    
    protected $logModel;

    protected $addressRepository;

    protected $productRepository;
    
    protected $orderRepository;

    protected $guestClientFactory;

    protected $gcCollectionFactory;

    protected $customerAccountManagement;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Websoft\Daftra\Model\Log $logModel,	
        Config $configWriter,
		TypeListInterface $cacheType,
        GuestClientFactory $guestClientFactory,
        CollectionFactory $gcCollectionFactory,
        AccountManagementInterface $customerAccountManagement
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->orderModel = $orderModel;
        $this->customerRepositoryInterface = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->timezone = $timezone;
        $this->logModel = $logModel;
        $this->configWriter = $configWriter;
		$this->cacheType = $cacheType;
        $this->guestClientFactory = $guestClientFactory;
        $this->gcCollectionFactory = $gcCollectionFactory;
        $this->customerAccountManagement = $customerAccountManagement;
    }

        
    protected function getConfigValue($path,$storeId = 1){
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
    }

	public function isEnabled(){
		return $this->getConfigValue(self::MODULE_ENABLED);
	}
    public function isOrderSyncEnabled(){
		return $this->getConfigValue(self::ORDER_SYNC_ENABLED);
	}

    public function isRefundSyncEnabled(){
		return $this->getConfigValue(self::REFUND_SYNC_ENABLED);
	}

    public function isProductSyncEnabled(){
		return $this->getConfigValue(self::PRODUCT_SYNC_ENABLED);
	}


    public function getAccessToken(){
		return $this->getConfigValue(self::ACCESS_TOKEN);
	}

    public function getConfigTaxId()
	{
		return $this->getConfigValue(self::TAX_ID);
	}

    public function setSendTaxId($value)
	{
		$this->setConfig(self::TAX_ID, $value);
	}

    public function getPaymentFeeId()
	{
		return $this->getConfigValue(self::FEE);
	}

    public function setPaymentFeeId($value)
	{
		$this->setConfig(self::FEE, $value);
	}
    public function getIsDebugEnabled()
	{
		return $this->getConfigValue(self::DEBUG_ENABLED);
	}
    

	public function setConfig($field, $value) {

        $this->configWriter->saveConfig(
            $field,
			$value
        );
		$this->cacheType->cleanType('config');
    }
    

    public function log($recordID, $status,$endPoint,$request,$response){

		try {
			$logParams=array();
			$logParams['method']=$endPoint;
			$logParams['request']=$request;
			$logParams['response']=$response;
			$logParams['datetime']= $this->timezone->date()->format('Y-m-d H:i:s');
			$logParams['relatedorder']=$recordID;
			$logParams['status']=$status;

            $this->logModel->setData($logParams)->save();
			return true;
		} catch (Exception $e) { 
            $this->logger->error($e->getMessage());
			return false;
		}
	}

    
    public function makeRequest($request, $endPoint='invoices.json',$type = self::GET, $ref = null){

        $ch = curl_init('https://doobzi.daftra.com/api2/'.$endPoint);

        if($type == self::POST || $type == self::PUT){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'APIKEY:'.$this->getAccessToken()
            ]
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        if($result){
            $resultArray = json_decode($result,true);
            if(isset($resultArray['result']) && $resultArray['result'] == 'successful'){
                $this->log($ref, 1 ,$endPoint,$request,$result);
                return $result;
            }
        }  
        $this->log($ref, 0 ,$endPoint,$request,$result);

        return $result;
    }

    public function sendRefunds($order, $items , $all){
        $refundJson = $this->prepareRefund($order, $items,  $all);

        $result = $this->makeRequest($refundJson, 'refund_receipts.json', self::POST, $order->getIncrementId());
        $result = json_decode($result,true);
        if(isset($result['id'])){
            $order->setRefundId($result['id']);
            $order->save();

            $_order = $this->orderRepository->get($order->getId());
            $_order->setRefundId($result['id']);
            $_order->save();
        }
    }

    public function sendInvoices($order){ //,$from='invoice'){
        //$state = $order->getState();
        //if($state != 'processing' && $from == 'invoice'|| $state != 'processing' && $from == 'shipment'){
            $invoiceJson = $this->prepareInvoice($order);//,$from);
            $result = $this->makeRequest($invoiceJson, 'invoices.json', self::POST, $order->getIncrementId());
            $result = json_decode($result,true);
            if(isset($result['id'])){
                $order->setInvoiceId($result['id']);
                $order->save();

                $_order = $this->orderRepository->get($order->getId());
                $_order->setInvoiceId($result['id']);
                $_order->save();
            }
            
        // }else{
        //     $changeStatus = $this->prepairChangeStatus($order,$from,$state);
        // }
    }


    // public function prepairChangeStatus($order,$from,$state){
    //     if($from == 'shipment'){
    //         // add delivered
    //         // $json = '
    //         // {
    //         //     "Invoice": {
    //         //       "client_id": '.$this->syncClient($order).',
    //         //       "requisition_delivery_status": 1
    //         //     }
    //         // }';
    //         // $result = $this->makeRequest($json,'invoices/'.$order->getData("invoice_id"),self::PUT,$order->getIncrementId());
    //     }elseif($from == 'invoice'){
    //         // add payment     
    //         $json = '
    //         {
    //             "InvoicePayment": {
    //                 "invoice_id": '.$order->getData("invoice_id").',
    //                 "payment_method": "'.$order->getPayment()->getMethodInstance()->getCode().'",
    //                 "amount": '.$order->getGrandTotal().'
    //             }
    //         }';

    //         $result = $this->makeRequest($json,'invoice_payments.json',self::POST,$order->getIncrementId());

    //     }
    // }

    public function prepareRefund($order, $refundItems, $all = false){

        $order_id = $order->getIncrementId();
         echo  $order_id;

        $clientId = $this->syncClient($order);
         echo ' clientId'.$clientId;


        $refundArr["RefundReceipt"] = [
            "store_id" => "1",
            "client_id" =>  $clientId,
            "subscription_id" => $order->getData("invoice_id")
        ];

        $refundArr["InvoiceItem"]=[];
        $orderItemCollection = $order->getAllItems(); // invoice

        foreach ($refundItems as $refundItem) {
            foreach($refundItem->getAllItems() as $item){
                $productId = $this->syncProduct($item->getSku());
                if(!$productId) $productId = 5;

                //$this->makePurchase($item);
                $refundArr["InvoiceItem"][] = 
                    [
                        "unit_price" => round($item->getRowTotalInclTax()/$item->getQty()) == '2,45'? 2 : round($item->getRowTotalInclTax()/$item->getQty()), 
                        "quantity" => $item->getQty(), 
                        "product_id" => $productId, 
                    ] ;
            
            }
        }

        if($all == true){

            $shippingAmount = round($order->getShippingAmount() + $order->getShippingAmount() * 0.15);
            $refundArr["RefundReceipt"]['shipping_amount'] = $shippingAmount;
            

            $discountAmount = 0;
            if ($order->getDiscountAmount()) {
                $discountAmount = abs($order->getDiscountAmount());
                $refundArr["RefundReceipt"]['discount_amount'] = $discountAmount;
            }

            $paymentFee = $order->getPaymentFee();
            if($paymentFee > 0){
                $feeId = $this->getPaymentFeeId();
                if(!$feeId){
                    $feeId = $this->syncFee($paymentFee);
                }


                $refundArr["InvoiceItem"][] = 
                    [
                        "unit_price" => $paymentFee, 
                        "quantity" => 1, 
                        "product_id" => $feeId, 
                    ];
            }
        }

        return json_encode($refundArr);
    }


    public function sendRefund($creditMemo){
        $order = $creditMemo->getOrder();
        $refundJson = $this->prepareRefundEvent($order, $creditMemo);

        $result = $this->makeRequest($refundJson, 'refund_receipts.json', self::POST, $order->getIncrementId());
        $result = json_decode($result,true);
        if(isset($result['id'])){
            $order->setRefundId($result['id']);
            $order->save();

            $_order = $this->orderRepository->get($order->getId());
            $_order->setRefundId($result['id']);
            $_order->save();
        }
    }
    
    public function prepareRefundEvent($order, $creditmemo ){
        $order_id = $order->getIncrementId();
        $clientId = $this->syncClient($order);

        $refundArr["RefundReceipt"] = [
            "store_id" => "1",
            "client_id" =>  $clientId,
            "subscription_id" => $order->getData("invoice_id")
        ];

        $refundArr["InvoiceItem"]=[];
        $orderItemCollection = $creditmemo->getAllItems(); // invoice

        foreach($orderItemCollection as $item){
            $productId = $this->syncProduct($item->getSku());
            if(!$productId) $productId = 5;

            //$this->makePurchase($item);
            $refundArr["InvoiceItem"][] = 
                [
                    "unit_price" => round($item->getRowTotalInclTax()/$item->getQty()), 
                    "quantity" => $item->getQty(), 
                    "product_id" => $productId, 
                ] ;
        
        }
        
        $shippingAmount = $creditmemo->getShippingAmount();

        if($shippingAmount > 0){            
            $refundArr["RefundReceipt"]['shipping_amount'] = $shippingAmount;
        }

        $discountAmount = $creditmemo->getDiscountAmount();
        if ($discountAmount > 0) {
            $discountAmount = abs($discountAmount);
            $refundArr["RefundReceipt"]['discount_amount'] = $discountAmount;
        }

        $paymentFee = $creditmemo->getPaymentFee();
        if($paymentFee > 0){
            $feeId = $this->getPaymentFeeId();
            if(!$feeId){
                $feeId = $this->syncFee($paymentFee);
            }
            $refundArr["InvoiceItem"][] = 
            [
                "unit_price" => $paymentFee, 
                "quantity" => 1, 
                "product_id" => $feeId, 
            ];
        }

        return json_encode($refundArr);
    }

    public function prepareInvoice($order){ //,$from){
        $BillingAddress = $order->getBillingAddress();
        $discountAmount = 0;
        if ($order->getDiscountAmount()) {
            $discountAmount = abs($order->getDiscountAmount());
        }
        $shippingAmount = round($order->getShippingAmount() + $order->getShippingAmount() * 0.15);

        $order_id = $order->getIncrementId();
        // echo  $order_id;
        $createdAt = $this->timezone->date(new \DateTime($order->getUpdatedAt()));
        $dateCreatedAt = $createdAt->format('Y-m-d');

        $clientId = $this->syncClient($order);
        // echo ' clientId'.$clientId;

        $InvoiceArr["Invoice"] = [
                "staff_id" => 0, 
                "subscription_id" => null, 
                "store_id" => 0, 
                //"no" => null, 
                "order_number" => $order_id, 
                "invoice_order_no" => $order_id, 
                //"name" => "string", 
                "client_id" => $clientId, 
                "is_offline" => true, 
                "currency_code" => "SAR", 
                "client_business_name" => $BillingAddress->getFirstname() . ' ' . $BillingAddress->getLastname(), 
                "client_first_name" => $BillingAddress->getFirstname(), 
                "client_last_name" => $BillingAddress->getLastname(), 
                "client_email" => $order->getCustomerEmail(), 
                "client_address1" => implode(" ",$BillingAddress->getStreet()), 
                "client_address2" => "", 
                "client_postal_code" => $BillingAddress->getPostcode(), 
                "client_city" => $BillingAddress->getCity(), 
                "client_state" => "", 
                "client_country_code" => $BillingAddress->getCountryId(), 
                "date" => $dateCreatedAt, 
                "draft" => "0", 
                "discount" => 0, 
                "discount_amount" => $discountAmount, 
                "deposit" => 0, 
                "deposit_type" => 0, 
                "notes" => "", 
                "html_notes" => "", 
                "invoice_layout_id" => null, 
                "estimate_id" => 0, 
                "shipping_options" => "2", 
                "shipping_amount" => $shippingAmount, 
                "client_active_secondary_address" => 0, 
                "client_secondary_name" => "", 
                "client_secondary_address1" => "", 
                "client_secondary_address2" => "", 
                "client_secondary_city" => "", 
                "client_secondary_state" => "", 
                "client_secondary_postal_code" => "", 
                "client_secondary_country_code" => "", 
                "follow_up_status" => null, 
                "work_order_id" => null, 
                "requisition_delivery_status" => 1, 
                "pos_shift_id" => null 
        ];

        $taxId = $this->getTaxId();
        $InvoiceArr["InvoiceItem"]=[];
        $itemCollection = $order->getAllItems(); // invoice
        foreach($itemCollection as $item){

            $productId = $this->syncProduct($item->getSku());


            //$this->makePurchase($item);
            $InvoiceArr["InvoiceItem"][] = 
                [
                    "invoice_id" =>  $order_id, 
                    "item" => $item->getName(), 
                    "description" => $item->getSku(), 
                    "unit_price" => round($item->getRowTotalInclTax()/$item->getQtyOrdered()), 
                    "quantity" => $item->getQtyOrdered(), 
                    "tax1" => $taxId, 
                    "tax2" => 0, 
                    "product_id" => $productId, 
                    "col_3" => null, 
                    "col_4" => null, 
                    "col_5" => null, 
                    "discount" => 0, 
                    "discount_type" => "0", 
                    "store_id" => 0 
                ] ;
        
        }
       

        $paymentFee = $order->getPaymentFee();
        if($paymentFee > 0){
            $feeId = $this->getPaymentFeeId();
            if(!$feeId){
                $feeId = $this->syncFee($paymentFee);
            }


            $InvoiceArr["InvoiceItem"][] = 
                [
                    "invoice_id" =>  $order_id, 
                    "item" => 'cash on delivery payment fee', 
                    "description" => 'cod fee', 
                    "unit_price" => $paymentFee, 
                    "quantity" => 1, 
                    "tax1" => 0, 
                    "tax2" => 0, 
                    "product_id" => $feeId, 
                    "col_3" => null, 
                    "col_4" => null, 
                    "col_5" => null, 
                    "discount" => 0, 
                    "discount_type" => "0", 
                    "store_id" => 0 
                ];
        }

        //if($from == 'invoice'){
            $InvoiceArr["Payment"] = [];
            $InvoiceArr["Payment"][] =
                [
                    "payment_method" => $order->getPayment()->getMethodInstance()->getCode(), 
                    "amount" => $order->getGrandTotal(), 
                    "transaction_id" => $order_id, 
                    "date" => $createdAt, 
                    "staff_id" => 0 
                ] 
            ;
        //}

        //if($from == 'shipment'){
            $InvoiceArr["Invoice"]['requisition_delivery_status'] = 1;
        //}

        return json_encode($InvoiceArr);
    }



    public function getTaxId(){
        $taxId = '';

        if($taxId = $this->getConfigTaxId()){
            return $taxId;
        }

        // $taxJson = '{"Tax": {
        //     "name": "fullTax",
        //     "value": 15,
        //     "description": "full tax",
        //     "included": 1
        // }}';

        // $result = $this->makeRequest($taxJson,'taxes.json',self::POST);

        // if($result){
        //     $resultArray = json_decode($result,true);
        //     if($resultArray['result'] == 'successful'){
        //         $taxId = $resultArray['id'];
        //         $this->setSendTaxId($taxId);
        //     }
        // }

        // return $taxId;
    }

    // public function getInvoice($order){

    //     $result = $this->makeRequest('','invoices/,self::GET);

    // }

    public function syncClient($order){

        $name = $order->getCustomerName();
        $email = $order->getCustomerEmail();
        $customer = null;
        if($customerId = $order->getCustomerId()){
            // echo 'customer';
            $customer = $this->customerRepositoryInterface->getById($customerId);
            if($customer->getCustomAttribute('daftra_id')){
                $clientId = $customer->getCustomAttribute('daftra_id')->getValue();
                return $clientId;
            }

        }
        if($customerId = $this->getGeustClientByEmail($email)){
            // echo 'GeustClient';
            return $customerId;
        }else{
            // echo 'fromlist';
            // $customerId = $this->getGeustClientLastId();
            // echo ' **'.$customerId.'** ';
            // $result = $this->makeRequest('','clients?limit=10000',self::GET);
            // $result = json_decode($result, true);
            // foreach( $result['data'] as $client){
            //     if(isset($client['Client']['email']) && $client['Client']['email'] == $email){
            //         $this->addGeustClient($client['Client']['email'], $client['Client']['id']);
            //         return $client['Client']['id'];
            //     }
            // }

            $customerId = rand(10000,100000);
        }
        //if(!$customerId) echo 'not found';

        $shippingAddress = $order->getShippingAddress();
        $mobileNumber = $shippingAddress->getTelephone();



        
        // $shippingAddress = $this->addressRepository->getById($shippingAddressId);
        $region = $shippingAddress->getRegion();
        $city = $shippingAddress->getCity(); 
        $postalCode = $shippingAddress->getPostcode(); 
        $countryCode = $shippingAddress->getCountryId();
		$address = implode(" ",$shippingAddress->getStreet());
        $firstName = $shippingAddress->getFirstname();
        $lastName = $shippingAddress->getLastname();

        $request['Client'] = [];
        $request['Client']['is_offline'] = true;
        $request['Client']['client_number'] = $customerId;
        $request['Client']['staff_id'] = 0;
        $request['Client']['business_name'] = $name;
        $request['Client']['first_name'] = $firstName;
        $request['Client']['last_name'] = $lastName;
        $request['Client']['email'] = $email;
        $request['Client']['password'] = $customerId;
        $request['Client']['address1'] = $address;
        $request['Client']['city'] = $city;
        $request['Client']['state'] = $region;
        $request['Client']['postal_code'] = $postalCode;
        $request['Client']['phone1'] = $mobileNumber;
        $request['Client']['country_code'] = $countryCode;
        $request['Client']['type'] = 2;

        $result = $this->makeRequest(json_encode($request),'clients.json',self::POST,$customerId);

        $clientId = '';
        if($result){
            $resultArray = json_decode($result,true);
            if($resultArray['result'] == 'successful'){
                $clientId = $resultArray['id'];
                if($customer){
                    $customer->setCustomAttribute('daftra_id', $clientId);
                    $this->customerRepositoryInterface->save($customer);
                    $this->addGeustClient($email, $clientId);
                }else{
                    $this->addGeustClient($email, $clientId);
                }
            }
        }

        return $clientId;

    }

    public function syncAllGeust(){
        $result = $this->makeRequest('','clients?limit=1000',self::GET);
        $result = json_decode($result, true);
        foreach( $result['data'] as $client){
            if(isset($client['Client']['email'])){
                //$isEmailNotExists = $this->customerAccountManagement->isEmailAvailable($client['Client']['email'], 1);
                //if(!$isEmailNotExists){
                    $this->addGeustClient($client['Client']['email'], $client['Client']['id']);
                //}
            }
        }
    }

    public function syncProduct($sku){

        if( $sku == '12914') $sku = '5019';

        try {
            $product = $this->productRepository->get($sku,false,2);
        } catch (Exception $e) {
            return null;
        }
        // if($productId = $product->getData('daftra_id')){

        //     return $productId;
        // }

        $name = $product->getName();

        $taxId = $this->getTaxId();


        $request['Product'] = [];
        $request['Product']['staff_id'] = 0;
        $request['Product']['name'] = $product->getName();
        $request['Product']['unit_price'] = $product->getFinalPrice();
        $request['Product']['tax1'] = $this->getTaxId();
        $request['Product']['brand'] = $product->getAttributeText('factory');
        $request['Product']['product_code'] = $product->getSku();
        $request['Product']['barcode'] = $product->getData('barcode1');
        $request['Product']['description'] = $product->getData('barcode2');
        $request['Product']['tags'] = $product->getData('barcode3');
        $request['Product']['track_stock'] = 1;
        $request['Product']['tracking_type'] = "quantity_only";
        $request['Product']['stock_balance'] = 1;
        $request['Product']['low_stock_thershold'] = 0;
        $request['Product']['supplier_id'] = 1; // needs set
        $request['Product']['type'] = 1; // needs set



        if($productId = $product->getData('daftra_id')){
            $this->makeRequest(json_encode($request),'products/'.$productId,self::PUT,$product->getId());
            $product->setData('updated',1);
            $this->productRepository->save($product);
        }else{
            $result = $this->makeRequest(json_encode($request),'products.json',self::POST,$product->getId());
            $productId = '';
            if($result){
                $resultArray = json_decode($result,true);
                if($resultArray['result'] == 'successful'){
                    $productId = $resultArray['id'];
                    $product->setData('daftra_id',$productId);
                    $this->productRepository->save($product);
                }
            }
        }


        return $productId;

    }

    public function syncFee($paymentFee){
        $request['Product'] = [];
        $request['Product']['staff_id'] = 0;
        $request['Product']['name'] = 'رسوم إضافية لطريقة الدفع عند اللإستلام';
        $request['Product']['unit_price'] = $paymentFee;
        $request['Product']['product_code'] = 99999;
        // $request['Product']['track_stock'] = 1;
        // $request['Product']['tracking_type'] = "quantity_only";
        // $request['Product']['stock_balance'] = 1;
        // $request['Product']['low_stock_thershold'] = 0;
        // $request['Product']['supplier_id'] = 1; // needs set
        // $request['Product']['type'] = 1; // needs set



        $result = $this->makeRequest(json_encode($request),'products.json',self::POST,99999);

        $productId = '';
        if($result){
            $resultArray = json_decode($result,true);
            if($resultArray['result'] == 'successful'){
                $productId = $resultArray['id'];
                $this->setPaymentFeeId($productId);
            }
        }

        return $productId;

    }


    public function makePurchase($item){
        $product = $item->getProduct();
        $purchaseId = $product->getData('purchase_id') > 0 ?  $product->getData('purchase_id') : 1;
        $purchaseId = $product->getId() + ($purchaseId) * 100000;

        $productId = $product->getData('daftra_id');

        $request['PurchaseOrder'] = [];
        $request['PurchaseOrder']['id'] = $purchaseId;
        $request['PurchaseOrder']['staff_id'] = 0;
        $request['PurchaseOrder']['no'] = $purchaseId;
        $request['PurchaseOrder']['supplier_id'] = true;
        $request['PurchaseOrder']['is_offline'] = true;
        $request['PurchaseOrder']['currency_code'] = "SAR";
        $request['PurchaseOrder']['is_received'] = true;

        $request['PurchaseOrderItem'] = [];
        $request['PurchaseOrderItem']['product_id'] = $productId;
        $request['PurchaseOrderItem']['org_name'] = $product->getName();
        $request['PurchaseOrderItem']['description'] = $product->getDescription();
        $request['PurchaseOrderItem']['unit_price'] = $product->getFinalPrice() - $product->getFinalPrice() * 0.2;
        $request['PurchaseOrderItem']['quantity'] = $item->getQty();
        $request['PurchaseOrderItem']['tax1'] = $this->getTaxId();


        \Magento\Framework\App\ObjectManager::getInstance()
        ->get(\Psr\Log\LoggerInterface::class)->info(json_encode($request));

        $result = $this->makeRequest(json_encode($request),'purchase_orders.json',self::POST,$productId);

        if($result){
            $resultArray = json_decode($result,true);
            if($resultArray['result'] == 'successful'){
                $_product = $this->productRepository->get($product->getSku());
                $_product->setData('purchase_id',$purchaseId++);
                $this->productRepository->save($_product);
            }
        }

    }

    public function getGeustClientByEmail($email){
        $collection = $this->gcCollectionFactory->create()
		->addFieldToSelect('*')
		->addFieldToFilter('email', '-'.$email);

        if($collection->getSize() > 0){
            $id = $collection->getLastItem()->getData('clientid');
            return $id;
        }
        // for each vendor first product
        return 0;
    }    

    
    public function getGeustClientLastId(){
        $collection = $this->gcCollectionFactory->create()
		->addFieldToSelect('*');

        if($collection->getSize() > 0){
            $id = $collection->getLastItem()->getData('clientid');
            // echo ' '.$id.' ';
        }else{
            $id = 9999;
            ' '.$id.' ';
        }
        // for each vendor first product
        return ++$id;
    } 

    public function addGeustClient($email, $clientid){
        $guestClient = $this->guestClientFactory->create();
        $guestClient->setData('email', '-'.$email);
        $guestClient->setData('clientid', $clientid);
        $guestClient->save();
    }
}

