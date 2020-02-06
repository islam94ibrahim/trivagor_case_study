<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Item extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['reputationBadge'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'availability' => 'integer',
        'user_id' => 'integer'
    ];

    public function getReputationBadgeAttribute()
    {
        return $this->reputation <= 500 ? 'red' : ($this->reputation <=799 ? 'yellow' : 'green');
    }

    /**
     * Get the user that owns the item.
     */
    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the location that owns the item.
     */
    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    public function scopeOwned($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeMinAvailability($query, $min)
    {
        return $query->where('availability', '>=', $min);
    }

    public function scopeMaxAvailability($query, $max)
    {
        return $query->where('availability', '<=', $max);
    }
}
