<?php namespace App\Models\ReportFeeds;

/**
 * Class ReportFeeds
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\ReportFeeds\Traits\Attribute\Attribute;
use App\Models\ReportFeeds\Traits\Relationship\Relationship;

class ReportFeeds extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_report_feeds";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "feed_id", "description", "created_at", "updated_at", 
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