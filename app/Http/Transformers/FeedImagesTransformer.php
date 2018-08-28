<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class FeedImagesTransformer extends Transformer
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
            "feedimagesId" => (int) $item->id, "feedimagesFeedId" =>  $item->feed_id, "feedimagesImage" =>  $item->image, "feedimagesCreatedAt" =>  $item->created_at, "feedimagesUpdatedAt" =>  $item->updated_at, 
        ];
    }
}