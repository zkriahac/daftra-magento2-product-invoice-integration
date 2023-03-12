<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Api\Data;

interface LogInterface
{

    const STATUS = 'status';
    const DATETIME = 'datetime';
    const LOG_ID = 'log_id';
    const METHOD = 'method';
    const RELATEDORDER = 'relatedorder';
    const REQUEST = 'request';
    const RESPONSE = 'response';

    /**
     * Get log_id
     * @return string|null
     */
    public function getLogId();

    /**
     * Set log_id
     * @param string $logId
     * @return \Websoft\Daftra\Log\Api\Data\LogInterface
     */
    public function setLogId($logId);

    /**
     * Get method
     * @return string|null
     */
    public function getMethod();

    /**
     * Set method
     * @param string $method
     * @return \Websoft\Daftra\Log\Api\Data\LogInterface
     */
    public function setMethod($method);

    /**
     * Get request
     * @return string|null
     */
    public function getRequest();

    /**
     * Set request
     * @param string $request
     * @return \Websoft\Daftra\Log\Api\Data\LogInterface
     */
    public function setRequest($request);

    /**
     * Get response
     * @return string|null
     */
    public function getResponse();

    /**
     * Set response
     * @param string $response
     * @return \Websoft\Daftra\Log\Api\Data\LogInterface
     */
    public function setResponse($response);

    /**
     * Get datetime
     * @return string|null
     */
    public function getDatetime();

    /**
     * Set datetime
     * @param string $datetime
     * @return \Websoft\Daftra\Log\Api\Data\LogInterface
     */
    public function setDatetime($datetime);

    /**
     * Get relatedorder
     * @return string|null
     */
    public function getRelatedorder();

    /**
     * Set relatedorder
     * @param string $relatedorder
     * @return \Websoft\Daftra\Log\Api\Data\LogInterface
     */
    public function setRelatedorder($relatedorder);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Websoft\Daftra\Log\Api\Data\LogInterface
     */
    public function setStatus($status);
}

