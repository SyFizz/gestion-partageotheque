<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStatus extends Model
{

    protected $fillable = ['name', 'slug'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
