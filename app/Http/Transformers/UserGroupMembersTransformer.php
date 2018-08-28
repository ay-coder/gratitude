<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class UserGroupMembersTransformer extends Transformer
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
            "usergroupmembersId" => (int) $item->id, "usergroupmembersUserId" =>  $item->user_id, "usergroupmembersMemberId" =>  $item->member_id, "usergroupmembersCreatedAt" =>  $item->created_at, "usergroupmembersUpdatedAt" =>  $item->updated_at, 
        ];
    }
}