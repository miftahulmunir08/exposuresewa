<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Product extends Model
{
    use HasFactory, HasApiTokens, SoftDeletes;

    protected $table = 'master_product';  // Make sure this matches your table name
    protected $primaryKey = 'uuid';
    protected $keyType = 'string'; // Pastikan tipe data sesuai
    public $incrementing = false;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'uuid');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->kode_barang)) {
                $product->kode_barang = self::generateKodeBarang();
            }

            if (empty($product->status)) {
                $product->status = 'tersedia';
            }
        });
    }

    public static function generateKodeBarang()
    {
        $lastProduct = self::orderByRaw("LENGTH(kode_barang) DESC, kode_barang DESC")->first();

        if ($lastProduct && preg_match('/PRD-(\d+)/', $lastProduct->kode_barang, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return 'PRD-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
