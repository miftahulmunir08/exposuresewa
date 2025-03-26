<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class StockLog extends Model
{
    use HasFactory, HasApiTokens, SoftDeletes;

    protected $table = 'data_product_stock_log';  // Make sure this matches your table name

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'uuid');
    }

}
