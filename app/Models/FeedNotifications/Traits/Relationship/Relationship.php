<?php namespace App\Models\FeedNotifications\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Feeds\Feeds;

trait Relationship
{
	/**
	 * @return mixed
	 */
	public function user()
	{
	    return $this->belongsTo(User::class, 'user_id');
	}

	/**
	 * @return mixed
	 */
	public function from_user()
	{
	    return $this->belongsTo(User::class, 'from_user_id');
	}

	/**
	 * @return mixed
	 */
	public function feed()
	{
	    return $this->belongsTo(Feeds::class, 'feed_id');
	}
}