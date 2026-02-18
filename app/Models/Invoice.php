<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'reference_date',
        'closing_date',
        'due_date',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'reference_date' => 'date',
            'closing_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isOpen(): bool
    {
        return $this->status === InvoiceStatus::OPEN->value;
    }

    public function isClosed(): bool
    {
        return $this->status === InvoiceStatus::CLOSED->value;
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::PAID->value;
    }
}
