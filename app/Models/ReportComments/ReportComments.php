<?php namespace App\Models\ReportComments;

/**
 * Class ReportComments
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\ReportComments\Traits\Attribute\Attribute;
use App\Models\ReportComments\Traits\Relationship\Relationship;

class ReportComments extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_report_comments";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "reporter_id", "feed_id", "comment_id", "comment", "created_at", "updated_at", 
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