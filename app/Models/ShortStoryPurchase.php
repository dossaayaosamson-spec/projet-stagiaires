<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortStoryPurchase extends Model
{
    protected $fillable = [
        'user_id',
        'short_story_id',
        'amount',
        'transaction_reference',
        'status',
        'idempotency_key',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shortStory()
    {
        return $this->belongsTo(ShortStory::class);
    }
}
