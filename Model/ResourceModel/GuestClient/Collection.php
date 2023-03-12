<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Model\ResourceModel\GuestClient;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'guestclient_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Websoft\Daftra\Model\GuestClient::class,
            \Websoft\Daftra\Model\ResourceModel\GuestClient::class
        );
    }
}

