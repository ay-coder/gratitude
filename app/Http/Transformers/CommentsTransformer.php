<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

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
            "commentsId" => (int) $item->id, "commentsUserId" =>  $item->user_id, "commentsFeedId" =>  $item->feed_id, "commentsComment" =>  $item->comment, "commentsStatus" =>  $item->status, "commentsCreatedAt" =>  $item->created_at, "commentsUpdatedAt" =>  $item->updated_at, 
        ];
    }

    /**
     * Transform Feed Comments
     * 
     * @param object $items
     * @return array
     */
    public function transformFeedComments($items)
    {
        $response = [];

        if(isset($items) && count($items))
        {
            foreach($items as $item) 
            {
                $response[] = [
                    'comment_id' => (int) $item->id,
                    'feed_id'    => (int) $item->feed_id,
                    'user_id'    => (int) $item->user->id,
                    'comment'    => $item->comment,
                    'profile_pic'   =>  URL::to('/').'/uploads/user/' . $item->user->profile_pic,
                    'create_at'  => date('m/d/Y h:i:s', strtotime($item->created_at))
                ];
            }
        }

        return $response;
    }
}