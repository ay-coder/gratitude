<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

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


    /**
     * Block Users Transform
     * 
     * @param array  $users
     * @return array
     */
    public function blockUsersTransform($users = array())
    {
        $response = [];

        if($users)
        {
            foreach($users as $user)
            {
                $response[] = [
                    'user_id'       => (int) $user->id,
                    'name'          => $this->nulltoBlank($user->name),
                    'email'         => $this->nulltoBlank($user->email),
                    'phone'         => $this->nulltoBlank($user->phone),
                    'profile_pic'   => isset($user->profile_pic) ? URL::to('/').'/uploads/user/' . $user->profile_pic : '',
                    'bio'           => $this->nulltoBlank($user->bio),
                ];
            }
        }

        return $response;
    }
}