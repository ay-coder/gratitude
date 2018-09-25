<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class ReportFeedsTransformer extends Transformer
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
            "reportfeedsId" => (int) $item->id, "reportfeedsUserId" =>  $item->user_id, "reportfeedsFeedId" =>  $item->feed_id, "reportfeedsDescription" =>  $item->description, "reportfeedsCreatedAt" =>  $item->created_at, "reportfeedsUpdatedAt" =>  $item->updated_at, 
        ];
    }
}