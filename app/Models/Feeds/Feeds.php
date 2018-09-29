<?php namespace App\Models\Feeds;

/**
 * Class Feeds
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Feeds\Traits\Attribute\Attribute;
use App\Models\Feeds\Traits\Relationship\Relationship;

class Feeds extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_feeds";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "group_id", "is_individual", "category_id", "feed_type", "description", "created_at", "updated_at", 
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