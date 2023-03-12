<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GuestClientRepositoryInterface
{

    /**
     * Save GuestClient
     * @param \Websoft\Daftra\Api\Data\GuestClientInterface $guestClient
     * @return \Websoft\Daftra\Api\Data\GuestClientInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Websoft\Daftra\Api\Data\GuestClientInterface $guestClient
    );

    /**
     * Retrieve GuestClient
     * @param string $guestclientId
     * @return \Websoft\Daftra\Api\Data\GuestClientInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($guestclientId);

    /**
     * Retrieve GuestClient matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Websoft\Daftra\Api\Data\GuestClientSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete GuestClient
     * @param \Websoft\Daftra\Api\Data\GuestClientInterface $guestClient
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Websoft\Daftra\Api\Data\GuestClientInterface $guestClient
    );

    /**
     * Delete GuestClient by ID
     * @param string $guestclientId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($guestclientId);
}

