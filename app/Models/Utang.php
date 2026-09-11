<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Utang extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'total_amount',
        'amount_paid',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->total_amount - (float) $this->amount_paid;
    }

    /**
     * Recalculate and persist this utang's status based on amount_paid.
     */
    public function refreshStatus(): void
    {
        if ($this->amount_paid <= 0) {
            $this->status = 'unpaid';
        } elseif ($this->amount_paid >= $this->total_amount) {
            $this->status = 'paid';
        } else {
            $this->status = 'partial';
        }

        $this->save();
    }
}
