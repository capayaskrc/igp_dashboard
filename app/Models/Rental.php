<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'owner_id',
        'rent_price',
        'category_id',
        'start_date',
        'due_date',
        'paid_for_this_month'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'paid_for_this_month' => 'boolean',
    ];

    /**
     * Get the owner of the rental.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Scope a query to only include rentals that are due soon.
     */
    public function scopeDueSoon($query)
    {
        return $query->where('due_date', '<=', now()->addDays(7));
    }

    /**
     * Determine if the rent is overdue.
     */
    public function isOverdue()
    {
        return $this->due_date->isPast() && !$this->paid_for_this_month;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
