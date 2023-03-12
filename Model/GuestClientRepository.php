<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Websoft\Daftra\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Websoft\Daftra\Api\Data\GuestClientInterface;
use Websoft\Daftra\Api\Data\GuestClientInterfaceFactory;
use Websoft\Daftra\Api\Data\GuestClientSearchResultsInterfaceFactory;
use Websoft\Daftra\Api\GuestClientRepositoryInterface;
use Websoft\Daftra\Model\ResourceModel\GuestClient as ResourceGuestClient;
use Websoft\Daftra\Model\ResourceModel\GuestClient\CollectionFactory as GuestClientCollectionFactory;

class GuestClientRepository implements GuestClientRepositoryInterface
{

    /**
     * @var ResourceGuestClient
     */
    protected $resource;

    /**
     * @var GuestClientInterfaceFactory
     */
    protected $guestClientFactory;

    /**
     * @var GuestClientCollectionFactory
     */
    protected $guestClientCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var GuestClient
     */
    protected $searchResultsFactory;


    /**
     * @param ResourceGuestClient $resource
     * @param GuestClientInterfaceFactory $guestClientFactory
     * @param GuestClientCollectionFactory $guestClientCollectionFactory
     * @param GuestClientSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceGuestClient $resource,
        GuestClientInterfaceFactory $guestClientFactory,
        GuestClientCollectionFactory $guestClientCollectionFactory,
        GuestClientSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->guestClientFactory = $guestClientFactory;
        $this->guestClientCollectionFactory = $guestClientCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(GuestClientInterface $guestClient)
    {
        try {
            $this->resource->save($guestClient);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the guestClient: %1',
                $exception->getMessage()
            ));
        }
        return $guestClient;
    }

    /**
     * @inheritDoc
     */
    public function get($guestClientId)
    {
        $guestClient = $this->guestClientFactory->create();
        $this->resource->load($guestClient, $guestClientId);
        if (!$guestClient->getId()) {
            throw new NoSuchEntityException(__('GuestClient with id "%1" does not exist.', $guestClientId));
        }
        return $guestClient;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->guestClientCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(GuestClientInterface $guestClient)
    {
        try {
            $guestClientModel = $this->guestClientFactory->create();
            $this->resource->load($guestClientModel, $guestClient->getGuestclientId());
            $this->resource->delete($guestClientModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the GuestClient: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($guestClientId)
    {
        return $this->delete($this->get($guestClientId));
    }
}

