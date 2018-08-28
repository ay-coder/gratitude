<?php namespace App\Models\Notes;

/**
 * Class Notes
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Notes\Traits\Attribute\Attribute;
use App\Models\Notes\Traits\Relationship\Relationship;

class Notes extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_personal_notes";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "notes", "created_at", "updated_at", 
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