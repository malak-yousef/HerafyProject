<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'user_name',
        'email',
        'email_verified_at',
        'password',
        'mobile',
        'gender',
        'status',
        'image',
        'address',
        'date',
        'actor_type',
        'actor_id',
        'role_name',
        'google_id',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function isAdmin(): bool
    {
        return is_null($this->actor_type)
            && is_null($this->actor_id)
            && $this->roles()->where('guard_name', 'admin')->exists();
    }

    /** Limit a query to identities managed as CMS administrators. */
    public function scopeCmsAdmin(Builder $query): Builder
    {
        return $query
            ->whereNull('actor_type')
            ->whereNull('actor_id')
            ->whereHas('roles', fn (Builder $roles) => $roles->where('guard_name', 'admin'));
    }

    public function isCustomer(): bool
    {
        return $this->actor_type === Customer::class
            && $this->actor instanceof Customer;
    }

    public function isCraftman(): bool
    {
        return $this->actor_type === Craftman::class
            && $this->actor instanceof Craftman;
    }

    public function profilePhotoPath(): ?string
    {
        if (! $this->image) {
            return null;
        }

        $path = str_replace('\\', '/', trim($this->image));

        if (preg_match('#^https?://#i', $path)) {
            $path = (string) parse_url($path, PHP_URL_PATH);
        }

        foreach (['/storage/app/public/', '/public/storage/', '/storage/'] as $prefix) {
            if (($position = stripos($path, $prefix)) !== false) {
                $path = substr($path, $position + strlen($prefix));
                break;
            }
        }

        $path = ltrim($path, '/');

        if (! str_contains($path, '/')) {
            $directory = $this->actor_type === Customer::class ? 'images/customer' : 'images/craftman';
            $path = $directory.'/'.$path;
        }

        return $path;
    }

    public function profilePhotoUrl(): ?string
    {
        $path = $this->profilePhotoPath();

        return $path ? asset('storage/'.$path) : null;
    }

    public function reportsMade()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function reportsReceived()
    {
        return $this->hasMany(Report::class, 'reported_id');
    }
}
