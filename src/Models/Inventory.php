<?php

namespace VisioSoft\LaraAnsible\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'hostname',
        'port',
        'username',
        'keystore_id',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'port' => 'integer',
    ];

    public function keystore(): BelongsTo
    {
        return $this->belongsTo(Keystore::class);
    }
}
