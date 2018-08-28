<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class FeedLoveTransformer extends Transformer
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
            "feedloveId" => (int) $item->id, "feedloveUserId" =>  $item->user_id, "feedloveFeedId" =>  $item->feed_id, "feedloveStatus" =>  $item->status, "feedloveCreatedAt" =>  $item->created_at, "feedloveUpdatedAt" =>  $item->updated_at, 
        ];
    }
}