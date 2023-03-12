<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GuestClient extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('websoft_daftra_guestclient', 'guestclient_id');
    }
}

