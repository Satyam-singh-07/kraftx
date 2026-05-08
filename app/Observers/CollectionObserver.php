<?php

namespace App\Observers;

use App\Models\Collection;
use App\Services\ShiprocketService;

class CollectionObserver
{
    protected $shiprocketService;

    public function __construct(ShiprocketService $shiprocketService)
    {
        $this->shiprocketService = $shiprocketService;
    }

    public function saved(Collection $collection)
    {
        $this->shiprocketService->syncCollection($collection);
    }
}
