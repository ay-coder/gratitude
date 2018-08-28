<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;
class UserGroupsTransformer extends Transformer
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
            "usergroupsId" => (int) $item->id, "usergroupsUserId" =>  $item->user_id, "usergroupsTitle" =>  $item->title, "usergroupsCreatedAt" =>  $item->created_at, "usergroupsUpdatedAt" =>  $item->updated_at, 
        ];
    }

    public function transformUserGroupsWithMembers($items)
    {
        $response = [];

        if(isset($items) && count($items))
        {
            foreach($items as $item)   
            {
                $members = [];

                if(isset($item->group_members))
                {
                    foreach($item->group_members as $member)
                    {
                        $isAdmin = $item->user_id == $member->user->id ? 1 : 0;
                        $members[] = [
                            'user_id'       => (int)  $member->user->id,
                            'is_admin'      => $isAdmin,
                            'username'      => $member->user->name,
                            'profile_pic'   => URL::to('/').'/uploads/user/' . $member->user->profile_pic,
                        ];
                    }
                }


                $response[] = [
                    'group_id'      => (int) $item->id,
                    'title'         => $item->title,
                    'admin_id'      => $item->user->id,
                    'admin_username' => $item->user->name,
                    'profile_pic'   => URL::to('/').'/uploads/user/' . $item->user->profile_pic,
                    'group_members' => $members
                ];
            }
        }

        return $response;        
    }
}