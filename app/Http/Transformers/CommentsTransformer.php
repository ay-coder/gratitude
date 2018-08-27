<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class CommentsTransformer extends Transformer
{
    /**
     * Transform
     *
     * @param array $data
     * @return array
     */
    public function transform($item)
    {
        if(is_array($item))
        {
            $item = (object)$item;
        }

        return [
            "comment_id"    => (int) $item->id,
            "user_id"       => (int) $item->user_id,
            "post_id"       => (int) $item->post_id,
            "comment"       =>  $item->comment
        ];
    }
}