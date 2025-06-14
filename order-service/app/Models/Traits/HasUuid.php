<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\Model;

trait HasUuid {
  protected static function bootHasUuid(): void {
    static::creating(function (Model $model): void {
      if (empty($model->uuid)) {
        $model->uuid = (string) Str::uuid();
      }
    });
  }
}
