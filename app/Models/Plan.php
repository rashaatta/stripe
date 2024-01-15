<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $type
 * @property float $price_monthly
 * @property float $price_yearly
 */
class Plan extends Model
{
    use SoftDeletes;
    use HasFactory;

    public const TYPE_SELECT = [
        'free' => 'Free',
        'paid' => 'Paid',
    ];

    public $table = 'plans';
     protected $casts = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'description',
        'price_monthly',
        'price_yearly',
        'stripe_monthly_plan',
        'stripe_yearly_plan',
        'type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getIsPaidAttribute(): bool
    {
        return $this->type == 'paid';
    }

    public function getIsFreeAttribute(): bool
    {
        return $this->type == 'free';
    }

    public function scopePaid($query)
    {
        return $query->where('type', 'paid');
    }
}
