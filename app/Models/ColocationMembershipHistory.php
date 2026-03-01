<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColocationMembershipHistory extends Model
{
    use HasFactory;

    protected $table = 'colocation_membership_history';

    protected $fillable = [
        'user_id',
        'colocation_id',
        'colocation_role',
        'joined_at',
        'left_at',
        'leave_reason',
        'debt_amount',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'debt_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function colocation(): BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeLeft($query)
    {
        return $query->whereNotNull('left_at');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('left_at');
    }
}
