<?php namespace App\Models\DirectFeeds;

/**
 * Class DirectFeeds
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\DirectFeeds\Traits\Attribute\Attribute;
use App\Models\DirectFeeds\Traits\Relationship\Relationship;

class DirectFeeds extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_individual_feeds";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "category_id", "phone", "email", "description", "created_at", "updated_at", 
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