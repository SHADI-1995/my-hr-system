<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'code',
        'module',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->code;
    }
}
