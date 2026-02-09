<?php

namespace App\Helpers;

use App\Models\ActionLog;

class Logger
{
    public static function log(string $action, $model, ?array $old = null, ?array $new = null): void
    {
        ActionLog::create([
            'refer_id' => $model->id ?? null,
            'model'    => class_basename($model),
            'action'   => $action,
            'old_data' => $old,
            'new_data' => $new,
            'ip'       => request()->ip(),
            'user_id'  => auth()->id(),
        ]);
    }
}
