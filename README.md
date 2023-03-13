# Magento2 Module Websoft Daftra

![alt text](https://play-lh.googleusercontent.com/8nvKcNeL5ELV6vaMD5N3grOA2D7xvHvNKmrsSCRUMIzImLvcuKRbcpfMmtka6Ryfeoo)

    ``websoft/module-daftra``

 - [Main Functionalities](#main-functionalities)
 - [Installation](#installation)
 - [Configuration](#configuration)
 - [Specifications](#specifications)
 - [Attributes](#attributes)


## Main Functionalities
Daftra integration

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Websoft/Daftra`
 - Enable the module by running `php bin/magento module:enable Websoft_Daftra`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require websoft/module-daftra`
 - enable the module by running `php bin/magento module:enable Websoft_Daftra`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - enable (general/general/enable)
 - Order Sync Enable (general/general/order_sync_enabled)           
 - Refund Sync Enable (general/general/refund_sync_enabled) 
 - Product Sync Enable (general/general/product_sync_enabled)
 - Token (general/general/token)              
 - Tax id (general/general/tax_id) 
 - Payment Fee related Product Id (general/general/payment_fee_id) 
 - Debug Enable (general/general/debug_enabled) 
            

## Specifications

 - Cronjob
	- websoft_daftra_sendorders
   - websoft_daftra_syncproducts

 - Events
   - sales_order_creditmemo_load_after

 - Helper
	- Websoft\Daftra\Helper\Data


## Attributes

 - Customer - Daftra Related ID (daftra_id)

 - Product - Daftra Related Id (daftra_id)


