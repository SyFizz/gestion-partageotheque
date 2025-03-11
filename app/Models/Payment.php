<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'payment_date',
        'expiry_date',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
