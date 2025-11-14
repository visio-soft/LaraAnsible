<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deployment extends Model
{
    protected $fillable = [
        'task_template_id',
        'environment_id',
        'user_id',
        'inventory_ids',
        'status',
        'console_output',
        'started_at',
        'completed_at',
        'exit_code',
    ];

    protected $casts = [
        'inventory_ids' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'exit_code' => 'integer',
    ];

    public function taskTemplate(): BelongsTo
    {
        return $this->belongsTo(TaskTemplate::class);
    }

    public function environment(): BelongsTo
    {
        return $this->belongsTo(Environment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
