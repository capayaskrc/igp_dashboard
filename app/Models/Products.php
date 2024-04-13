<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'initial_quantity', 'current_quantity', 'inventory_id'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
