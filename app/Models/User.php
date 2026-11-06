<?php

namespace App\Models;

use App\Notifications\VerifyEmailCode;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'prenom',
        'nom_famille',
        'email',
        'telephone',
        'pays',
        'password',
        'newsletter',
        'role',
        'verification_code',
        'alias',
        'otp_code',
        'otp_expires_at',
    ];

    /**
     * Surcharge de l'envoi de notification de vérification d'email
     */
    public function sendEmailVerificationNotification()
    {
        if (! $this->verification_code) {
            $this->generateVerificationCode();
        }

        $this->notify(new VerifyEmailCode($this->verification_code));
    }

    /**
     * Génère un code de vérification à 6 chiffres
     */
    public function generateVerificationCode()
    {
        $this->verification_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->save();
    }

    /**
     * Génère un code OTP à 6 chiffres pour la connexion 2FA
     */
    public function generateOtpCode(): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->forceFill([
            'otp_code' => $code,
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        return $code;
    }

    /**
     * Vérifie si le code OTP est valide et non expiré
     */
    public function verifyOtpCode(string $code): bool
    {
        if (empty($this->otp_code) || empty($this->otp_expires_at)) {
            return false;
        }

        if ($this->otp_code !== trim($code)) {
            return false;
        }

        if (now()->isAfter($this->otp_expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Efface le code OTP après utilisation
     */
    public function clearOtpCode(): void
    {
        $this->forceFill([
            'otp_code' => null,
            'otp_expires_at' => null,
        ])->save();
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'newsletter' => 'boolean',
            'otp_expires_at' => 'datetime',
        ];
    }

    public function author()
    {
        return $this->hasOne(Author::class);
    }

    // Relations existantes
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('statut', 'actif')->latest();
    }

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom_famille}";
    }

    // Ajout des relations pour les favoris
    public function favoris()
    {
        return $this->hasMany(Favori::class);
    }

    public function livresFavoris()
    {
        return $this->belongsToMany(Book::class, 'favoris', 'user_id', 'livre_id')
            ->withTimestamps();
    }

    public function aEnFavori($livreId): bool
    {
        return $this->favoris()->where('livre_id', $livreId)->exists();
    }

    //  Progression de lecture

    public function booksProgress()
    {
        return $this->belongsToMany(Book::class, 'book_user_progress', 'user_id', 'book_id')
            ->withPivot(['progress', 'status', 'last_read_at'])
            ->withTimestamps();
    }

    public function bookRatings()
    {
        return $this->hasMany(BookRating::class);
    }

    // Alias pour booksProgress()
    public function readers()
    {
        return $this->booksProgress();
    }

    public function booksInProgress()
    {
        return $this->booksProgress()
            ->wherePivot('status', 'reading')
            ->orderByPivot('last_read_at', 'desc');
    }

    public function booksFinished()
    {
        return $this->booksProgress()
            ->wherePivot('status', 'finished');
    }

    public function getProgressForBook(int $bookId): int
    {
        $entry = $this->booksProgress()
            ->where('book_id', $bookId)
            ->first();

        return $entry ? (int) $entry->pivot->progress : 0;
    }

    public function latestReadBook()
    {
        return $this->booksProgress()
            ->orderByPivot('last_read_at', 'desc')
            ->first();
    }

    // ----------------------------------------------------------------
    // Relations blog & modération
    // ----------------------------------------------------------------

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function suspensions()
    {
        return $this->hasMany(UserSuspension::class);
    }

    public function activeSuspension()
    {
        return $this->hasOne(UserSuspension::class)->whereNull('lifted_at')->latest();
    }

    public function isSuspended(): bool
    {
        return $this->suspensions()->whereNull('lifted_at')->exists();
    }

    public function manuscripts()
    {
        return $this->hasMany(Manuscript::class);
    }

    public function isAuthor(): bool
    {
        return in_array(strtolower($this->role ?? ''), ['auteur', 'author'], true);
    }

    public function isAdmin(): bool
    {
        return strtolower($this->role ?? '') === 'admin';
    }

    public function authorApplications()
    {
        return $this->hasMany(Manuscript::class)->where('submission_source', 'candidature_auteur');
    }
}
