<?php namespace App\Models\FeedTagUsers;

/**
 * Class FeedTagUsers
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\FeedTagUsers\Traits\Attribute\Attribute;
use App\Models\FeedTagUsers\Traits\Relationship\Relationship;

class FeedTagUsers extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "feed_tag_users";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "feed_id", "group_id", "created_at", "updated_at", 
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