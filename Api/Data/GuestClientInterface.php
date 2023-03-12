<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Api\Data;

interface GuestClientInterface
{

    const EMAIL = 'email';
    const CLIENTID = 'clientid';
    const GUESTCLIENT_ID = 'guestclient_id';

    /**
     * Get guestclient_id
     * @return string|null
     */
    public function getGuestclientId();

    /**
     * Set guestclient_id
     * @param string $guestclientId
     * @return \Websoft\Daftra\GuestClient\Api\Data\GuestClientInterface
     */
    public function setGuestclientId($guestclientId);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Websoft\Daftra\GuestClient\Api\Data\GuestClientInterface
     */
    public function setEmail($email);

    /**
     * Get clientid
     * @return string|null
     */
    public function getClientid();

    /**
     * Set clientid
     * @param string $clientid
     * @return \Websoft\Daftra\GuestClient\Api\Data\GuestClientInterface
     */
    public function setClientid($clientid);
}

