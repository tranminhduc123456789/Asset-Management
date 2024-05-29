<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'product_name', 'description', 'price', 'stock_quantity', 'sku',
    ];

    //Set relationship with store
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
