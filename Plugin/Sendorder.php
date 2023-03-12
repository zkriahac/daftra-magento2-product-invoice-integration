<?php

namespace  Websoft\Daftra\Plugin;

use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;

class Sendorder
{
  /** @var \Magento\Framework\UrlInterface */
  protected $_urlBuilder;

  /** @var \Magento\Framework\AuthorizationInterface */
  protected $_authorization;

  public function __construct(
    UrlInterface $url,
    AuthorizationInterface $authorization
  ) {
    $this->_urlBuilder = $url;
    $this->_authorization = $authorization;
  }

  public function beforeSetLayout(OrderView $view) {
		$message ='Are you sure you want to send this order to Logo?';
		$url = $this->_urlBuilder->getUrl('websoft_daftra/order/sendorder', ['id' => $view->getOrderId()]);

		$view->addButton(
		  'websoft_daftra_sendorder',
		  [
			'label' => __('Send invoice'),
			'class' => 'sendorder primary',
			'onclick' => "confirmSetLocation('{$message}', '{$url}')"
		  ]
		);
  }
}