<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'price',
        'initial_quantity',
        'current_quantity'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // You can also define any additional methods or functionality here
    // For example, a method to check stock status
    public function isLowStock()
    {
        return $this->current_quantity <= 10; // Assuming low stock is defined as 10 or fewer items
    }
}
