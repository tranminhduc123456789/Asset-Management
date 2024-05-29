<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'store_name', 'store_address', 'store_phone', 'store_email',
    ];
    //Set relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Set relationship with product
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
