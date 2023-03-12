<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface LogRepositoryInterface
{

    /**
     * Save Log
     * @param \Websoft\Daftra\Api\Data\LogInterface $log
     * @return \Websoft\Daftra\Api\Data\LogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Websoft\Daftra\Api\Data\LogInterface $log
    );

    /**
     * Retrieve Log
     * @param string $logId
     * @return \Websoft\Daftra\Api\Data\LogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($logId);

    /**
     * Retrieve Log matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Websoft\Daftra\Api\Data\LogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Log
     * @param \Websoft\Daftra\Api\Data\LogInterface $log
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Websoft\Daftra\Api\Data\LogInterface $log
    );

    /**
     * Delete Log by ID
     * @param string $logId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($logId);
}

