<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    public function colocations(): BelongsToMany
    {
        return $this->belongsToMany(Colocation::class)
            ->withPivot('colocation_role')
            ->withTimestamps();
    }

    public function hasActiveColocation(): bool
    {
        return $this->colocations()->exists();
    }

    public function getActiveColocation(): ?Colocation
    {
        return $this->colocations()->first();
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'payer_id');
    }

    public function paymentsSent(): HasMany
    {
        return $this->hasMany(Payment::class, 'payer_id');
    }

    public function paymentsReceived(): HasMany
    {
        return $this->hasMany(Payment::class, 'receiver_id');
    }

    public function colocationHistory(): HasMany
    {
        return $this->hasMany(ColocationMembershipHistory::class);
    }

    public function oldColocations(): HasMany
    {
        return $this->colocationHistory()->left()->with('colocation');
    }

    public function updateReputation(float $change): void
    {
        $this->reputation += $change;
        $this->save();
    }

    public function decreaseReputation(): void
    {
        $this->updateReputation(-1.0);
    }

    public function increaseReputation(): void
    {
        $this->updateReputation(1.0);
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'reputation',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'reputation' => 'decimal:2',
            'role' => 'string',
            'status' => 'string',
        ];
    }

 
}
