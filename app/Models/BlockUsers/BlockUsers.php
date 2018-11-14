<?php namespace App\Models\BlockUsers;

/**
 * Class BlockUsers
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\BlockUsers\Traits\Attribute\Attribute;
use App\Models\BlockUsers\Traits\Relationship\Relationship;

class BlockUsers extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_block_users";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "block_user_id", "created_at", "updated_at", 
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