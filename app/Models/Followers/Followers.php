<?php namespace App\Models\Followers;

/**
 * Class Followers
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Followers\Traits\Attribute\Attribute;
use App\Models\Followers\Traits\Relationship\Relationship;

class Followers extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_followers";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "follower_id", "created_at", "updated_at", 
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