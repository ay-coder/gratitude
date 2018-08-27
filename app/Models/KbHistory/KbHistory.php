<?php namespace App\Models\KbHistory;

/**
 * Class KbHistory
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\KbHistory\Traits\Attribute\Attribute;
use App\Models\KbHistory\Traits\Relationship\Relationship;

class KbHistory extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "history";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "type_id", "user_id", "entity_id", "icon", "class", "text", "assets", "created_at", "updated_at", 
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