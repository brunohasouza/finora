<?php

namespace App\Models;

use App\Enums\WalletTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    /** @use HasFactory<\Database\Factories\WalletFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'balance',
        'user_id',
        'bank_id',
        'type',
        'credit_limit',
        'closing_day',
        'due_day',
    ];

    protected $appends = ['available_limit'];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function isCreditCard(): bool
    {
        return $this->type === WalletTypes::CREDIT_CARD->value;
    }

    public function getAvailableLimitAttribute(): int
    {
        if (!$this->isCreditCard()) {
            return 0;
        }

        $openInvoicesTotal = $this->invoices()
            ->whereIn('status', ['open', 'closed'])
            ->sum('total');

        return $this->credit_limit - $openInvoicesTotal;
    }
}
