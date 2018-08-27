<?php namespace App\Models\Categories;

/**
 * Class Categories
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Categories\Traits\Attribute\Attribute;
use App\Models\Categories\Traits\Relationship\Relationship;

class Categories extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "post_categories";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "title", "icon", "status", "created_at", "updated_at", 
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