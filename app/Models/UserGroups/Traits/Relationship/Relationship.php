<?php namespace App\Models\UserGroups\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\UserGroupMembers\UserGroupMembers;

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
	public function group_members()
	{
	    return $this->hasMany(UserGroupMembers::class, 'group_id');
	}
}