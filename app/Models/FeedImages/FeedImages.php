<?php namespace App\Models\FeedImages;

/**
 * Class FeedImages
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\FeedImages\Traits\Attribute\Attribute;
use App\Models\FeedImages\Traits\Relationship\Relationship;

class FeedImages extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_feed_images";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "feed_id", "image", "created_at", "updated_at", 
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