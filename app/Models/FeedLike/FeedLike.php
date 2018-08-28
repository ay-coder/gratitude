<?php namespace App\Models\FeedLike;

/**
 * Class FeedLike
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\FeedLike\Traits\Attribute\Attribute;
use App\Models\FeedLike\Traits\Relationship\Relationship;

class FeedLike extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_feed_likes";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "feed_id", "status", "created_at", "updated_at", 
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