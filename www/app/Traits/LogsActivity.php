<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        // Dispara depois de criar o registro
        static::created(function ($model) {
            $model->captureActivity('created', [], $model->getAttributes());
        });

        // Dispara antes de atualizar o registro
        static::updating(function ($model) {
            $oldValues = $model->fresh()->getAttributes();
            $newValues = $model->getAttributes();

            $differences = array_diff_assoc($newValues, $oldValues);
            // Se não houver alterações significativas, pode ignorar
            if (!empty($differences)) {
                $model->captureActivity('updated', $oldValues, $newValues);
            }
        });

        // Dispara antes de apagar o registro
        static::deleting(function ($model) {
            $oldValues = $model->getAttributes();
            $model->captureActivity('deleted', $oldValues, []);
        });
    }

    /**
     * Registra o log em banco
     *
     * @param  string  $event
     * @param  array   $oldValues
     * @param  array   $newValues
     * @return void
     */
    protected function captureActivity($event, array $oldValues, array $newValues)
    {
        ActivityLog::create([
            'model_type' => get_class($this),
            'model_id'   => $this->id ?? null, //$this->getKey(),
            'event'      => $event,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($newValues),
            //'caused_by'  => Auth::id() ?? null, // se quiser capturar o usuário logado
        ]);
    }
}
