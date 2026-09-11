<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function utangs(): HasMany
    {
        return $this->hasMany(Utang::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }




    public function getBalanceAttribute(): float
    {
        return (float) $this->utangs()
        ->whereIn('status', ['unpaid', 'partial'])
        ->sum(DB::raw('total_amount - amount_paid'));
    }

    public function openUtangs()
    {
        return $this->utangs()
        ->whereIn('status', ['unpaid', 'partial'])
        ->orderBy('created_at')
        ->get();
    }
}
