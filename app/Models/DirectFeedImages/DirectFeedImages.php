<?php namespace App\Models\DirectFeedImages;

/**
 * Class DirectFeedImages
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\DirectFeedImages\Traits\Attribute\Attribute;
use App\Models\DirectFeedImages\Traits\Relationship\Relationship;

class DirectFeedImages extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_direct_feed_images";

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