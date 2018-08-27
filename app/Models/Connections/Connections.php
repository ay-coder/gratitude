<?php namespace App\Models\Connections;

/**
 * Class Connections
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Connections\Traits\Attribute\Attribute;
use App\Models\Connections\Traits\Relationship\Relationship;

class Connections extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_connections";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "other_user_id", "requested_user_id", "is_accepted", "is_read", "created_at", "updated_at", 
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