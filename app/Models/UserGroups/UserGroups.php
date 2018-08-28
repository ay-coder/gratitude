<?php namespace App\Models\UserGroups;

/**
 * Class UserGroups
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\UserGroups\Traits\Attribute\Attribute;
use App\Models\UserGroups\Traits\Relationship\Relationship;

class UserGroups extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "user_groups";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "title", "created_at", "updated_at", 
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