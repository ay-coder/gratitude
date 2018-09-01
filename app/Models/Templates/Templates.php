<?php namespace App\Models\Templates;

/**
 * Class Templates
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Templates\Traits\Attribute\Attribute;
use App\Models\Templates\Traits\Relationship\Relationship;

class Templates extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_feed_templates";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "body", "created_at", "updated_at", 
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