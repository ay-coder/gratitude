<?php namespace App\Models\Comments\Traits\Relationship;


use App\Models\Access\User\User;
use App\Models\Posts\Posts;

trait Relationship
{
	/**
     * Belongs to relations with User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Belongs to relations with Posts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id');
    }
}

