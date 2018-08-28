<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class FeedTagUsersTransformer extends Transformer
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
            "feedtagusersId" => (int) $item->id, "feedtagusersUserId" =>  $item->user_id, "feedtagusersFeedId" =>  $item->feed_id, "feedtagusersCreatedAt" =>  $item->created_at, "feedtagusersUpdatedAt" =>  $item->updated_at, 
        ];
    }
}