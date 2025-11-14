<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Keystore extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'private_key',
        'public_key',
        'passphrase',
        'password',
    ];

    protected $hidden = [
        'private_key',
        'passphrase',
        'password',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
