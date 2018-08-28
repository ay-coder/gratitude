<?php namespace App\Models\Feeds\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\FeedImages\FeedImages;
use App\Models\Comments\Comments;
use App\Models\FeedLove\FeedLove;
use App\Models\FeedLike\FeedLike;
use App\Models\Categories\Categories;
use App\Models\FeedTagUsers\FeedTagUsers;

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
	    return $this->hasMany(FeedImages::class, 'feed_id');
	}

	/**
	 * @return mixed
	 */
	public function feed_comments()
	{
	    return $this->hasMany(Comments::class, 'feed_id');
	}

	/**
	 * @return mixed
	 */
	public function feed_loves()
	{
	    return $this->hasMany(FeedLove::class, 'feed_id');
	}

	/**
	 * @return mixed
	 */
	public function feed_likes()
	{
	    return $this->hasMany(FeedLike::class, 'feed_id');
	}

	/**
	 * @return mixed
	 */
	public function feed_tag_users()
	{
	    return $this->hasMany(FeedTagUsers::class, 'feed_id');
	}
}