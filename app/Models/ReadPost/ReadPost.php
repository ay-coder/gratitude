<?php namespace App\Models\ReadPost;

/**
 * Class ReadPost
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\ReadPost\Traits\Attribute\Attribute;
use App\Models\ReadPost\Traits\Relationship\Relationship;

class ReadPost extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_user_read_posts";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        
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

    public $dates = ['created_at', 'updated_at'];
}