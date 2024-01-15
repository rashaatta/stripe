<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $payment_frequency
 * @property string $status
 * @property \App\Models\Plan $plan
 * @property int $image_usage
 * @property int $usage
 * @property bool $isMonthly
 */
class Subscription extends Model
{
    use SoftDeletes;
    use HasFactory;

    public const ACTIVE = 'active';
    public const PENDING = 'pending';
    public const EXPIRED = 'expired';
    public const CANCELED = 'canceled';
    public const MONTHLY = 'monthly';
    public const YEARLY = 'yearly';

    public const PAYMENT_FREQUENCY_SELECT = [
        self::MONTHLY => 'Monthly',
        self::YEARLY  => 'Yearly',
    ];

    public const STATUS_SELECT = [
        self::PENDING  => 'Pending',
        self::ACTIVE   => 'Active',
        self::EXPIRED  => 'Expired',
        self::CANCELED => 'Canceled',
    ];

    public $table = 'subscriptions';

    protected $casts = [
        'start_at',
        'end_at',
        'canceled_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_at',
        'end_at',
        'canceled_at',
        'status',
        'payment_frequency',
        'pp_subscription',
        'stripe_subscription',
        'usage',
        'image_usage',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'subscription_id', 'id');
    }

    public function getIsMonthlyAttribute(): bool
    {
        return $this->payment_frequency == self::MONTHLY;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status == self::ACTIVE;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status == self::EXPIRED;
    }

    public function getIsCanceledAttribute(): bool
    {
        return $this->status == self::CANCELED;
    }

    public function getHasReachedLimitAttribute(): bool
    {
        return $this->usage >= $this->plan->word_limit;
    }

    public function getHasReachedImageLimitAttribute(): bool
    {
        return $this->image_usage >= $this->plan->image_limit;
    }

    public function setStartAtAttribute($value)
    {
        $this->attributes['start_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setEndAtAttribute($value)
    {
        $this->attributes['end_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setCanceledAtAttribute($value)
    {
        $this->attributes['canceled_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getTotalAmountAttribute(): float
    {
        if ($this->isMonthly) {
            return $this->plan->price_monthly;
        } else {
            return $this->plan->price_yearly;
        }
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::ACTIVE);
    }
}
