<?php

namespace Vanguard\UserActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vanguard\User;
use Vanguard\UserActivity\Database\Factories\ActivityFactory;

class Activity extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'user_activity';

    protected $fillable = ['description', 'user_id', 'ip_address', 'user_agent'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ActivityFactory
    {
        return new ActivityFactory;
    }
}
