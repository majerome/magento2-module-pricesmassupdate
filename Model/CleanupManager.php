<?php

declare(strict_types=1);

namespace Majerome\PricesMassUpdate\Model;

use Magento\Framework\App\Cache\Manager as CacheManager;
        // use Magento\Framework\App\Cache\TypeListInterface;

class CleanupManager
{
    public function __construct(
        // private readonly TypeListInterface $cacheTypeList,
        private readonly CacheManager      $cacheManager,
    ) {}

    public function clearCache(): void
    {
        // You can clean specific cache type(s)
        // $this->cacheTypeList->cleanType('full_page');

        // You can also clear all caches if needed
        $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
    }
}