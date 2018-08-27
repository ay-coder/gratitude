<?php

namespace App\Models\Access\User\Traits\Relationship;

use App\Models\Event\Event;
use App\Models\System\Session;
use App\Models\Access\User\SocialLogin;
use App\Models\Connections\Connections;
use App\Models\Posts\Posts;
use App\Models\Notifications\Notifications;

/**
 * Class UserRelationship.
 */
trait UserRelationship
{
    /**
     * Many-to-Many relations with Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('access.role'), config('access.role_user_table'), 'user_id', 'role_id');
    }

    /**
     * @return mixed
     */
    public function providers()
    {
        return $this->hasMany(SocialLogin::class);
    }

    /**
     * @return mixed
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * @return mixed
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return mixed
     */
    public function connections()
    {
        return $this->hasMany(Connections::class, 'user_id');
    } 

    /**
     * @return mixed
     */
    public function my_connections()
    {
        return $this->hasMany(Connections::class, 'user_id')
            ->where('is_accepted', 1);
    }


    /**
     * @return mixed
     */
    public function my_connection_requests()
    {
        return $this->hasMany(Connections::class, 'user_id')
            ->where('is_accepted', 0);
    }

    /**
     * @return mixed
     */
    public function accepted_connections()
    {
        return $this->hasMany(Connections::class, 'other_user_id')
            ->where('is_accepted', 1);
    } 

    /**
     * @return mixed
     */
    public function posts()
    {
        return $this->hasMany(Posts::class, 'user_id')->where('is_accepted', 1);
    }

    /**
     * @return mixed
     */
    public function user_posts()
    {
        return $this->hasMany(Posts::class, 'tag_user_id')->where('is_accepted', 1);
    }

    /**
     * @return mixed
     */
    public function post_requests()
    {
        return $this->hasMany(Posts::class, 'tag_user_id')->where('is_accepted', 0);
    }

    /**
     * @return mixed
     */
    public function tag_posts()
    {
        return $this->hasMany(Posts::class, 'tag_user_id');
    } 

    /**
     * @return mixed
     */
    public function user_notifications()
    {
        return $this->hasMany(Notifications::class, 'user_id');
    }    
}
