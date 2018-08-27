<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class BlockedTransformer extends Transformer
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
            "blockedId" => (int) $item->id, "blockedBlockedBy" =>  $item->blocked_by, "blockedUserId" =>  $item->user_id, "blockedPostId" =>  $item->post_id, "blockedComment" =>  $item->comment, "blockedCreatedAt" =>  $item->created_at, "blockedUpdatedAt" =>  $item->updated_at, 
        ];
    }
}