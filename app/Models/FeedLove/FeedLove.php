<?php namespace App\Models\FeedLove;

/**
 * Class FeedLove
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\FeedLove\Traits\Attribute\Attribute;
use App\Models\FeedLove\Traits\Relationship\Relationship;

class FeedLove extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_feed_loves";

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