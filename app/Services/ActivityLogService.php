<?php
// app/Services/ActivityLogService.php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogService
{
    /**
     * Log user activity with a model
     *
     * @param string $action
     * @param mixed $model
     * @param string $description
     * @param array|null $changes
     * @return void
     */
    public static function log($action, $model, $description, $changes = null)
    {
        if (!Auth::check()) {
            return;
        }

        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->id ?? 0,
                'changes' => $changes,
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            Log::error('ActivityLog creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Log activity without model (like login/logout/register)
     *
     * @param string $action
     * @param string $description
     * @return void
     */
    public static function logAction($action, $description)
    {
        if (!Auth::check()) {
            return;
        }

        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => 'System',
                'model_id' => 0,
                'changes' => null,
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            Log::error('ActivityLog creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Log action with user ID (for cases where user might not be authenticated)
     *
     * @param string $action
     * @param string $description
     * @param int $userId
     * @return void
     */
    public static function logActionForUser($action, $description, $userId)
    {
        try {
            ActivityLog::create([
                'user_id' => $userId,
                'action' => $action,
                'model_type' => 'System',
                'model_id' => 0,
                'changes' => null,
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            Log::error('ActivityLog creation failed: ' . $e->getMessage());
        }
    }
}
