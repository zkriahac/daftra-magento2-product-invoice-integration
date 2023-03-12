# Mage2 Module Websoft Daftra

    ``websoft/module-daftra``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Daftra integration

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Websoft`
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


