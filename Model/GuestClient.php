<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Model;

use Magento\Framework\Model\AbstractModel;
use Websoft\Daftra\Api\Data\GuestClientInterface;

class GuestClient extends AbstractModel implements GuestClientInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Websoft\Daftra\Model\ResourceModel\GuestClient::class);
    }

    /**
     * @inheritDoc
     */
    public function getGuestclientId()
    {
        return $this->getData(self::GUESTCLIENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGuestclientId($guestclientId)
    {
        return $this->setData(self::GUESTCLIENT_ID, $guestclientId);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getClientid()
    {
        return $this->getData(self::CLIENTID);
    }

    /**
     * @inheritDoc
     */
    public function setClientid($clientid)
    {
        return $this->setData(self::CLIENTID, $clientid);
    }
}

