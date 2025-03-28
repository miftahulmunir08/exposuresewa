<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class TransactionCart extends Model
{
    use HasFactory, HasApiTokens, SoftDeletes;

    protected $table = 'data_cart';  // Make sure this matches your table name
    protected $primaryKey = 'uuid';
    protected $keyType = 'string'; // Pastikan tipe data sesuai
    public $incrementing = false;
    protected $guarded = [];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'uuid');
    }
}
