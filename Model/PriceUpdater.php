<?php

declare(strict_types=1);

namespace Majerome\PricesMassUpdate\Model;

use Exception;
use Magento\Catalog\Model\ResourceModel\Product\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class PriceUpdater
{
    public function __construct(
        private readonly Action            $productAction,
        private readonly CollectionFactory $productCollectionFactory
    ) {}

    /**
     * @throws Exception
     */
    public function updatePrices(int|float $targetPrice): void
    {
        $batchSize = 1000;
        $collection = $this->productCollectionFactory->create();
        $collection->setPageSize($batchSize);
        $pages = $collection->getLastPageNumber();

        for ($page = 1; $page <= $pages; $page++) {
            $collection->setCurPage($page);
            $collection->clear();
            $collection->load();

            $productIds = $collection->getAllIds();
            $this->productAction->updateAttributes(
                $productIds,
                ['price' => $targetPrice],
                0 // Use store ID 0 for global scope when updating a website-scoped attribute
            );
        }
    }
}