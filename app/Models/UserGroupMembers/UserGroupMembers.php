<?php namespace App\Models\UserGroupMembers;

/**
 * Class UserGroupMembers
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\UserGroupMembers\Traits\Attribute\Attribute;
use App\Models\UserGroupMembers\Traits\Relationship\Relationship;

class UserGroupMembers extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "user_group_members";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "group_id", "member_id", "created_at", "updated_at", 
    ];

    /**
     * Timestamp flag
     *
     */
    public $timestamps = true;

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}