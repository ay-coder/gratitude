<?php namespace App\Models\Notifications;

/**
 * Class Notifications
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Notifications\Traits\Attribute\Attribute;
use App\Models\Notifications\Traits\Relationship\Relationship;

class Notifications extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_notifications";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "to_user_id", "description", "is_read", "created_at", "updated_at", 
        'notification_type',
        'comment_id',
        'post_id'
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