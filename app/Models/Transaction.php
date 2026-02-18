<?php

namespace App\Models;

use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([TransactionObserver::class])]
class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'description',
        'amount',
        'date',
        'type',
        'category_id',
        'wallet_id',
        'user_id',
        'invoice_id',
    ];

    protected $hidden = [
        'user_id',
        'category_id',
        'wallet_id',
        'invoice_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $with = ['wallet', 'category'];

    protected function casts(): array
    {
        return [
            'date' => 'datetime:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function scopeFilter($query, array $filters) {
        if ($filters['type'] ?? false) {
            $query->where('type', $filters['type']);
        }

        if ($filters['category_id'] ?? false) {
            $query->where('category_id', $filters['category_id']);
        }
    }
}
