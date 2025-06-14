<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

trait HasUserAction {
    use SoftDeletes;

    public static function bootHasUserActionMongo(): void {
        $getUserId = fn() => Auth::id();

        static::creating(function (Model $model) use ($getUserId) {
            if (empty($model->created_by)) {
                $model->created_by = $getUserId();
            }
        });
        
        static::updating(function (Model $model) use ($getUserId) {
            if (empty($model->updated_by)) {
                $model->updated_by = $getUserId();
            }
        });
        
        static::deleting(function (Model $model) use ($getUserId) {
            if ($model->isForceDeleting()) {
                return;
            }
            $model->deleted_by = $getUserId();
        });
    }

    public function initializeHasUserActionMongo(): void {
        $this->mergeFillable(['created_by', 'updated_by', 'deleted_by']);
        $this->dates[] = $this->getDeletedAtColumn();
    }
}
