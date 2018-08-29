<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class DirectFeedImagesTransformer extends Transformer
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
            "directfeedimagesId" => (int) $item->id, "directfeedimagesFeedId" =>  $item->feed_id, "directfeedimagesImage" =>  $item->image, "directfeedimagesCreatedAt" =>  $item->created_at, "directfeedimagesUpdatedAt" =>  $item->updated_at, 
        ];
    }
}