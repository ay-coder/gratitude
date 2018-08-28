<?php namespace App\Models\FeedLove\Traits\Relationship;

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