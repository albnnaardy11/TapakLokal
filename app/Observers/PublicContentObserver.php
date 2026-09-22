<?php

namespace App\Observers;

use App\Services\PublicContentService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PublicContentObserver implements ShouldHandleEventsAfterCommit
{
    public function saved(Model $model): void
    {
        Cache::forever(PublicContentService::VERSION_KEY, (string) Str::uuid());
    }

    public function deleted(Model $model): void
    {
        $this->saved($model);
    }
}
