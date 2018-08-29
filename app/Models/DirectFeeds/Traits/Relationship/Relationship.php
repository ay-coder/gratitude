<?php namespace App\Models\DirectFeeds\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\DirectFeedImages\DirectFeedImages;
use App\Models\Categories\Categories;

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
	public function feed_category()
	{
	    return $this->belongsTo(Categories::class, 'category_id');
	}

	/**
	 * @return mixed
	 */
	public function feed_images()
	{
	    return $this->hasMany(DirectFeedImages::class, 'feed_id');
	}
}