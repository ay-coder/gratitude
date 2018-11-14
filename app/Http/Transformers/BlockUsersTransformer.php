<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class BlockUsersTransformer extends Transformer
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
            "blockusersId" => (int) $item->id, "blockusersUserId" =>  $item->user_id, "blockusersBlockUserId" =>  $item->block_user_id, "blockusersCreatedAt" =>  $item->created_at, "blockusersUpdatedAt" =>  $item->updated_at, 
        ];
    }
}