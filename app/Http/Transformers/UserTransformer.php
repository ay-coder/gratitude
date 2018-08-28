<?php

namespace App\Http\Transformers;

use URL;
use App\Http\Transformers;

class UserTransformer extends Transformer 
{
    public function transform($data) 
    {
        $postRequestCount  = isset($data->post_requests) ? count($data->post_requests) : 0;
        return [
            'user_id'       => $data->id,
            'token'         => $this->nulltoBlank($data->token),
            'device_token'  => $data->device_token,
            'name'          => $this->nulltoBlank($data->name),
            'email'         => $this->nulltoBlank($data->email),
            'phone'         => $this->nulltoBlank($data->phone),
            'profile_pic'   => isset($data->profile_pic) ? URL::to('/').'/uploads/user/' . $data->profile_pic : '',
            
            'gender'        => $this->nulltoBlank($data->gender),
            
            'description'   => 'Lorem Ipusm Lorem Ipsum description',
            'connectionCount' => isset($data->connections) ? count($data->connections) : 0, 
            'postCount'     => isset($data->connections) ? count($data->connections) : 0, 
            'postRequestCount'  => (int) $postRequestCount,
            'notification_count' => (int) 0
        ];
    }
    
    public function userInfo($data)
    {
        $data = (object) $data;
        
        return [
            'user_id'       => $data->id,
            'username'      => $data->username,
            'token'         => isset($data->token) ? $this->nulltoBlank($data->token) : '',
            'device_token'  => $data->device_token,
            'name'          => $this->nulltoBlank($data->name),
            'email'         => $this->nulltoBlank($data->email),
            'phone'         => $this->nulltoBlank($data->phone),
            'profile_pic'   => isset($data->profile_pic) ? URL::to('/').'/uploads/user/' . $data->profile_pic : '',
            'dob'           => $this->nulltoBlank($data->dob),
            'bio'           => $this->nulltoBlank($data->bio),
            'description'   => $this->nulltoBlank($data->description),
            'address'       => $this->nulltoBlank($data->address),
            'city'          => $this->nulltoBlank($data->city),
            'zip'           => $this->nulltoBlank($data->zip),
        ];
    }

    public function getUserInfo($data) 
    {
        return [
            'userId'    => $data->id,
            'name'      => $this->nulltoBlank($data->name),
            'email'     => $this->nulltoBlank($data->email)
        ];
    }
    
    /**
     * userDetail
     * Single user detail
     * 
     * @param type $data
     * @return type
     */
    public function userDetail($data) {
        return [
            'UserId' => isset($data['id']) ? $data['id'] : "",
            'QuickBlocksId' => isset($data['quick_blocks_id']) ? $data['quick_blocks_id'] : "",
            'MobileNumber' => isset($data['mobile_number']) ? $data['mobile_number'] : "",
            'Name' => isset($data['username']) ? $data['username'] : "",
            'Specialty' => isset($data['specialty']) ? $data['specialty'] : "",
            'ProfilePhoto' => isset($data['profile_photo'])?$this->getUserImage($data['profile_photo']):""
        ];
    }

    /*
     * User Detail and it's parameters
     */
    public function singleUserDetail($data){        
        return [
            'UserId' => $data['id'],            
            'Name' => $this->nulltoBlank($data['name']),
            'Email' => $this->nulltoBlank($data['email']),
            'MobileNumber' => $this->nulltoBlank($data['mobile_number']),
        ];
    }
    
    public function transformStateCollection(array $items) {
        return array_map([$this, 'getState'], $items);

    }

    /**
     * Update User
     * 
     * @param object $data
     * @return array
     */
    public function updateUser($data)
    {
        $headerToken = request()->header('Authorization');
        $userToken   = '';

        if($headerToken)
        {
            $token      = explode(" ", $headerToken);
            $userToken  = $token[1];
        }

        return [
            'user_id'       => $data->id,
            'token'         => $this->nulltoBlank($data->token),
            'device_token'  => $data->device_token,
            'name'          => $this->nulltoBlank($data->name),
            'email'         => $this->nulltoBlank($data->email),
            'phone'         => $this->nulltoBlank($data->phone),
            'profile_pic'   => isset($data->profile_pic) ? URL::to('/').'/uploads/user/' . $data->profile_pic : '',
            'dob'           => $this->nulltoBlank($data->dob),
            'gender'        => $this->nulltoBlank($data->gender),
            'bio'           => $this->nulltoBlank($data->bio),
            'description'   => 'Lorem Ipusm Lorem Ipsum description',
            'connectionCount' => isset($data->connections) ? count($data->connections) : 0, 
            'postCount'     => isset($data->connections) ? count($data->connections) : 0, 
            'notification_count' => (int) 0
        ]; 
    }
}
