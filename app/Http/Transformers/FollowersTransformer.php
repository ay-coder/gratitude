<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

class FollowersTransformer extends Transformer
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
            "followersId" => (int) $item->id, "followersUserId" =>  $item->user_id, "followersFollowerId" =>  $item->follower_id, "followersCreatedAt" =>  $item->created_at, "followersUpdatedAt" =>  $item->updated_at, 
        ];
    }

    /**
     * Follower Transform
     * 
     * @param object $items
     * @return array
     */
    public function followerTransform($currentUser = null, $items)
    {
        $response = [];

        if(isset($items) && count($items))
        {
            foreach($items as $data)
            {
                if(isset($currentUser) && $currentUser->id == $data->id)
                    continue;
                
                $response[] = [
                    'user_id'       => (int) $data->id,
                    'name'          => $this->nulltoBlank($data->name),
                    'email'         => $this->nulltoBlank($data->email),
                    'phone'         => $this->nulltoBlank($data->phone),
                    'profile_pic'   => isset($data->profile_pic) ? URL::to('/').'/uploads/user/' . $data->profile_pic : '',
                    'bio'           => $this->nulltoBlank($data->bio),
                    'followings'    => (int) $data->followings
                ];
            }
        }

        return $response;
    }
    
    /**
     * Follower Transform
     * 
     * @param object $items
     * @return array
     */
    public function followerSuggestionTransform($currentUser = null, $items, $followerIds = array())
    {
        $response = [];

        if(isset($items) && count($items))
        {
            foreach($items as $data)
            {
                if(isset($currentUser) && $currentUser->id == $data->id)
                    continue;
                
                $response[] = [
                    'user_id'       => (int) $data->id,
                    'name'          => $this->nulltoBlank($data->name),
                    'email'         => $this->nulltoBlank($data->email),
                    'phone'         => $this->nulltoBlank($data->phone),
                    'profile_pic'   => isset($data->profile_pic) ? URL::to('/').'/uploads/user/' . $data->profile_pic : '',
                    'bio'           => $this->nulltoBlank($data->bio),
                    'followings'    => (int) $data->followings
                ];
            }
        }

        return $response;
    }
}