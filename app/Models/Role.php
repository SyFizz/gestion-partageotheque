<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $fillable = ['name', 'slug', 'description', 'requires_validation'];

    protected $casts = [
        'requires_validation' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }
}
