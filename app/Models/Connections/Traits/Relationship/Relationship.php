<?php namespace App\Models\Connections\Traits\Relationship;

use App\Models\Access\User\User;

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
     * Belongs to relations with Requested User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function other_user()
    {
        return $this->belongsTo(User::class, 'other_user_id');
    }    
}