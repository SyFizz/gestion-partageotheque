<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'identifier',
        'description',
        'category_id',
        'item_status_id',
        'caution_amount',
        'image_path',
        'notes',
        'is_archived'
    ];

    protected $casts = [
        'caution_amount' => 'decimal:2',
        'is_archived' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function status()
    {
        return $this->belongsTo(ItemStatus::class, 'item_status_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoans()
    {
        return $this->hasMany(Loan::class)->whereNull('return_date');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class)->where('is_active', true)
            ->orderBy('priority_order');
    }
}
