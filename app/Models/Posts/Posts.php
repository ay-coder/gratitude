<?php namespace App\Models\Posts;

/**
 * Class Posts
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Posts\Traits\Attribute\Attribute;
use App\Models\Posts\Traits\Relationship\Relationship;

class Posts extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_posts";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "tag_user_id", "media", "thumbnail", "description", "is_image", "is_video", "status", "created_at", "updated_at", 
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