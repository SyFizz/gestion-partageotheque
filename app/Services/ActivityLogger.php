<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log($action, $modelType, $modelId = null, $details = null)
    {
        if (!Auth::check()) {
            return;
        }

        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}
