<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Product;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'quantity_sold', 'total_amount'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Inventory::class);
    }
}
