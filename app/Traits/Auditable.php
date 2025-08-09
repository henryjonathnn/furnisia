<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        // Audit when model is created
        static::created(function ($model) {
            $model->createAuditRecord('create', null, $model->getAttributes());
        });

        // Audit when model is updated
        static::updated(function ($model) {
            $model->createAuditRecord('update', $model->getOriginal(), $model->getChanges());
        });

        // Audit when model is deleted
        static::deleted(function ($model) {
            $model->createAuditRecord('delete', $model->getAttributes(), null);
        });
    }

    /**
     * Create audit record
     */
    protected function createAuditRecord($event, $oldValues = null, $newValues = null)
    {
        // Skip if no authenticated user (e.g., seeder, console commands)
        if (!Auth::check()) {
            return;
        }

        // Filter out sensitive data
        $filteredOldValues = $this->filterSensitiveData($oldValues);
        $filteredNewValues = $this->filterSensitiveData($newValues);

        Audit::create([
            'user_id' => Auth::id(),
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'event' => $event,
            'old_values' => $filteredOldValues,
            'new_values' => $filteredNewValues,
        ]);
    }

    /**
     * Filter out sensitive data from audit
     */
    protected function filterSensitiveData($data)
    {
        if (!$data) return $data;

        // Remove sensitive fields
        $sensitiveFields = ['password', 'remember_token', 'password_confirmation'];
        
        return collect($data)->except($sensitiveFields)->toArray();
    }

    /**
     * Get all audits for this model (polymorphic relationship)
     */
    public function audits()
    {
        return $this->morphMany(Audit::class, 'auditable')->latest();
    }
}
