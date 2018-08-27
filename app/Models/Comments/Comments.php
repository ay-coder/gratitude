<?php namespace App\Models\Comments;

/**
 * Class Comments
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Comments\Traits\Attribute\Attribute;
use App\Models\Comments\Traits\Relationship\Relationship;

class Comments extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_comments";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "post_id", "comment", "created_at", "updated_at", 
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