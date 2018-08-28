<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class ReportCommentsTransformer extends Transformer
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
            "reportcommentsId" => (int) $item->id, "reportcommentsUserId" =>  $item->user_id, "reportcommentsFeedId" =>  $item->feed_id, "reportcommentsCommentId" =>  $item->comment_id, "reportcommentsComment" =>  $item->comment, "reportcommentsCreatedAt" =>  $item->created_at, "reportcommentsUpdatedAt" =>  $item->updated_at, 
        ];
    }
}