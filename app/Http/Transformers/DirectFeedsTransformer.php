<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class DirectFeedsTransformer extends Transformer
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
            "directfeedsId" => (int) $item->id, "directfeedsUserId" =>  $item->user_id, "directfeedsCategoryId" =>  $item->category_id, "directfeedsPhone" =>  $item->phone, "directfeedsEmail" =>  $item->email, "directfeedsDescription" =>  $item->description, "directfeedsCreatedAt" =>  $item->created_at, "directfeedsUpdatedAt" =>  $item->updated_at, 
        ];
    }
}