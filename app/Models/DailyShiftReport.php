<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyShiftReport extends Model
{
    protected $table = 'daily_shift_reports';
    public $timestamps = false;
    protected $guarded = [];

    // Since it's a view, it doesn't have an ID
    protected $primaryKey = 'date';
    public $incrementing = false;
    protected $keyType = 'string';
}
