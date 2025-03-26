<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Transaction extends Model
{
    use HasFactory, HasApiTokens, SoftDeletes;

    protected $table = 'data_transaction';  // Make sure this matches your table name

    protected $guarded = [];

    public static function generateTransactionCode()
    {
        do {
            $date = date('Ymd');
            $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            $code = "TRX-$date-$random";
        } while (self::where('transaction_code', $code)->exists());

        return $code;
    }
}
