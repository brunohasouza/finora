<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    /** @use HasFactory<\Database\Factories\BankFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'shortname',
        'name',
    ];

    public function accounts()
    {
        return $this->hasMany(Wallet::class);
    }
}
