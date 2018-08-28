<?php namespace App\Models\FeedLike\Traits\Relationship;

use App\Models\Access\User\User;

trait Relationship
{
	/**
	 * @return mixed
	 */
	public function user()
	{
	    return $this->belongsTo(User::class, 'user_id');
	}
}