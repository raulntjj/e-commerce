<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HasUserAction {
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public static function bootHasUserAction(): void {
        $userId = Auth::id();

        static::creating(function (Model $model) use ($userId) {
            if ($userId) {
                $model->created_by = $userId;
                Log::info('Model creating with user ID: ' . $userId);
            }
        });

        static::updating(function (Model $model) use ($userId) {
            if ($userId) {
                $model->updated_by = $userId;
                Log::info('Model updating with user ID: ' . $userId);
            }
        });

        static::deleting(function (Model $model) use ($userId) {
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                if ($userId) {
                    $model->deleted_by = $userId;
                    $model->save();
                    Log::info('Model deleting with user ID: ' . $userId);
                }
            }
        });
    }

    public function initializeHasUserAction(): void {
        $this->mergeFillable(['created_by', 'updated_by', 'deleted_by']);
    }
}