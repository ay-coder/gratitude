<?php namespace App\Models\Posts\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Comments\Comments;
use App\Models\ReadPost\ReadPost;

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
     * Belongs to relations with User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tag_user()
    {
        return $this->belongsTo(User::class, 'tag_user_id');
    }

    /**
     * HasMany to relations with Comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function comments()
    {
        return $this->hasMany(Comments::class, 'post_id');
    }

    /**
     * HasMany to relations with Views.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function views()
    {
        return $this->hasMany(ReadPost::class, 'post_id');
    }
}