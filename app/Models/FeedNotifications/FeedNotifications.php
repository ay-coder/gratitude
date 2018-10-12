<?php namespace App\Models\FeedNotifications;

/**
 * Class FeedNotifications
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\FeedNotifications\Traits\Attribute\Attribute;
use App\Models\FeedNotifications\Traits\Relationship\Relationship;

class FeedNotifications extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_feed_notifications";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "from_user_id", "is_clear", "feed_id", "description", "notification_type", "icon", "is_read", "created_at", "updated_at", 
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