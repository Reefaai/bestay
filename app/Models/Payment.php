<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_FAILED   = 'failed';
    public const STATUS_EXPIRED  = 'expired';
    public const STATUS_REFUNDED = 'refunded';

    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_E_WALLET      = 'e_wallet';
    public const METHOD_CREDIT_CARD   = 'credit_card';

    public const METHODS = [
        self::METHOD_BANK_TRANSFER,
        self::METHOD_E_WALLET,
        self::METHOD_CREDIT_CARD,
    ];

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PAID,
        self::STATUS_FAILED,
        self::STATUS_EXPIRED,
        self::STATUS_REFUNDED,
    ];

    public const TERMINAL_STATUSES = [
        self::STATUS_FAILED,
        self::STATUS_EXPIRED,
        self::STATUS_REFUNDED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_id',
        'reference',
        'amount',
        'method',
        'status',
        'failure_reason',
        'paid_at',
        'expires_at',
        'refunded_at',
        'verified_by',
        'verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'paid_at'     => 'datetime',
            'expires_at'  => 'datetime',
            'refunded_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the booking that owns the payment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the status logs for the payment.
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(PaymentStatusLog::class);
    }

    /**
     * Get the admin user who last verified the payment.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Determine if the payment has passed its expiry window.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Determine if the payment is in a terminal status.
     */
    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES);
    }

    /**
     * Determine if the payment is active (pending or paid).
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PAID]);
    }
}
