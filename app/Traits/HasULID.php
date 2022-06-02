<?php

namespace App\Traits;

use App\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Rorecek\Ulid\Facades\Ulid;

trait HasULID
{
    protected static function boot()
    {
        parent::boot();

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = Ulid::generate();
            }
        });

        static::saving(function ($model) {
            $originalUlid = $model->getOriginal('id');
            if ($originalUlid !== $model->id) {
                $model->id = $originalUlid;
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}