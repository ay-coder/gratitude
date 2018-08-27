<?php namespace App\Models\Students;

/**
 * Class Students
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Students\Traits\Attribute\Attribute;
use App\Models\Students\Traits\Relationship\Relationship;

class Students extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "students";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "name", "age", "std", 
    ];

    /**
     * Timestamp flag
     *
     */
    public $timestamps = false;

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}