<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Api\Data;

interface LogSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Log list.
     * @return \Websoft\Daftra\Api\Data\LogInterface[]
     */
    public function getItems();

    /**
     * Set method list.
     * @param \Websoft\Daftra\Api\Data\LogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

