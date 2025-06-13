<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasUuid {
  protected static function bootHasUuid(): void {
    static::creating(function ($model): void {
      if (empty($model->{$model->getKeyName()})) {
        $model->{$model->getKeyName()} = (string) Str::uuid();
      }
    });
  }

  public function initializeHasUuid(): void {
    $this->primaryKey = 'uuid';
    $this->keyType = 'string';
    $this->incrementing = false;
  }
}