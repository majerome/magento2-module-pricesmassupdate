<?php

declare(strict_types=1);

namespace Majerome\PricesMassUpdate\Model;

use Magento\Indexer\Model\Indexer\CollectionFactory as IndexerCollectionFactory;

class IndexerManager
{
    private array $indexerStates = [];

    public function __construct(
        private readonly IndexerCollectionFactory $indexerCollectionFactory,
    ) {}

    public function backupIndexerStates(): void
    {
        $indexerCollection = $this->indexerCollectionFactory->create();

        foreach ($indexerCollection as $indexer) {
            $this->indexerStates[$indexer->getId()] = $indexer->isScheduled();
        }
    }

    public function disableIndexers(): void
    {
        $indexerCollection = $this->indexerCollectionFactory->create();

        foreach ($indexerCollection as $indexer) {
            $indexer->setScheduled(true);
        }
    }

    public function reindexAll(): void
    {
        $indexerCollection = $this->indexerCollectionFactory->create();

        foreach ($indexerCollection as $indexer) {
            $indexer->reindexAll();
        }
    }

    public function restoreIndexerStates(): void
    {
        $indexerCollection = $this->indexerCollectionFactory->create();

        foreach ($indexerCollection as $indexer) {
            if (isset($this->indexerStates[$indexer->getId()])) {
                $indexer->setScheduled($this->indexerStates[$indexer->getId()]);
            }
        }
    }
}