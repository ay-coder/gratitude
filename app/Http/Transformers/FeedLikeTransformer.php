<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class FeedLikeTransformer extends Transformer
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
            "feedlikeId" => (int) $item->id, "feedlikeUserId" =>  $item->user_id, "feedlikeFeedId" =>  $item->feed_id, "feedlikeStatus" =>  $item->status, "feedlikeCreatedAt" =>  $item->created_at, "feedlikeUpdatedAt" =>  $item->updated_at, 
        ];
    }
}