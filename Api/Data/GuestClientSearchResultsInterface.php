<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Api\Data;

interface GuestClientSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get GuestClient list.
     * @return \Websoft\Daftra\Api\Data\GuestClientInterface[]
     */
    public function getItems();

    /**
     * Set email list.
     * @param \Websoft\Daftra\Api\Data\GuestClientInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

