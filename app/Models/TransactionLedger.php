<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLedger extends Model
{
    protected $table = 'transaction_ledgers';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $casts = [
        'date' => 'date',
        'amount' => 'float',
    ];
}
