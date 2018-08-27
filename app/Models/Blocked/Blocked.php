<?php namespace App\Models\Blocked;

/**
 * Class Blocked
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Blocked\Traits\Attribute\Attribute;
use App\Models\Blocked\Traits\Relationship\Relationship;

class Blocked extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_comment_blocked";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "blocked_by", "user_id", "post_id", "comment", "created_at", "updated_at", 
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