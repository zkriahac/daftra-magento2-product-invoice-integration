<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Model;

use Magento\Framework\Model\AbstractModel;
use Websoft\Daftra\Api\Data\LogInterface;

class Log extends AbstractModel implements LogInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Websoft\Daftra\Model\ResourceModel\Log::class);
    }

    /**
     * @inheritDoc
     */
    public function getLogId()
    {
        return $this->getData(self::LOG_ID);
    }

    /**
     * @inheritDoc
     */
    public function setLogId($logId)
    {
        return $this->setData(self::LOG_ID, $logId);
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return $this->getData(self::METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setMethod($method)
    {
        return $this->setData(self::METHOD, $method);
    }

    /**
     * @inheritDoc
     */
    public function getRequest()
    {
        return $this->getData(self::REQUEST);
    }

    /**
     * @inheritDoc
     */
    public function setRequest($request)
    {
        return $this->setData(self::REQUEST, $request);
    }

    /**
     * @inheritDoc
     */
    public function getResponse()
    {
        return $this->getData(self::RESPONSE);
    }

    /**
     * @inheritDoc
     */
    public function setResponse($response)
    {
        return $this->setData(self::RESPONSE, $response);
    }

    /**
     * @inheritDoc
     */
    public function getDatetime()
    {
        return $this->getData(self::DATETIME);
    }

    /**
     * @inheritDoc
     */
    public function setDatetime($datetime)
    {
        return $this->setData(self::DATETIME, $datetime);
    }

    /**
     * @inheritDoc
     */
    public function getRelatedorder()
    {
        return $this->getData(self::RELATEDORDER);
    }

    /**
     * @inheritDoc
     */
    public function setRelatedorder($relatedorder)
    {
        return $this->setData(self::RELATEDORDER, $relatedorder);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}

