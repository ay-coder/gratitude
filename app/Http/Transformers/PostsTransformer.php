<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;


class PostsTransformer extends Transformer
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

        $item->tag_user = (object)$item->tag_user;

        return [
            "post_id"       => (int) $item->id, 
            "user_id"       => (int) $item->user_id,
            "tag_user_id"   => (int) $item->tag_user_id,
            "media"         =>  URL::to('/').'/uploads/media/' . $item->media, 
            "thumbnail"     =>  isset($item->thumbnail) ? URL::to('/').'/uploads/media/' . $item->thumbnail : '',  
            "description"   =>  $item->description,
            "is_image"      => (int) $item->is_image,
            "is_video"      => (int) $item->is_video,
            'name'          => $this->nulltoBlank($item->tag_user->name),
            'email'         => $this->nulltoBlank($item->tag_user->email),
            'phone'         => $this->nulltoBlank($item->tag_user->phone),
            'viewCount'     => isset($item->views) ? count($item->views) : 0,
            'view_count'    => isset($item->views) ? count($item->views) : 0,
            'profile_pic'   => isset($item->tag_user->profile_pic) ? URL::to('/').'/uploads/user/' . $item->tag_user->profile_pic : '',
         ];
    }

    public function getUserPosts($user, $items)
    {
        $userReadPostIds    = [];
        $userReadPosts      = access()->getReadPostIds($user->id);

        if($userReadPosts)
        {   
            $userReadPostIds = array_values($userReadPosts->unique()->toArray());
        }

        $response           = [];
        $response['unread'] = [];
        $response['read']   = [];


        foreach($items as $item)
        {
            $item->user = (object)$item->user;
            $isRead     = in_array($item->id, $userReadPostIds) ? 1 :0;

            if($item->is_accepted == 0)
            {
                continue;
            }
            
            if($isRead == 0) 
            {
                $response['unread'][] = [
                    "post_id"       => (int) $item->id, 
                    "user_id"       => (int) $item->user_id,
                    "tag_user_id"   => (int) $item->tag_user_id,
                    "media"         =>  URL::to('/').'/uploads/media/' . $item->media, 
                    "thumbnail"     =>  isset($item->thumbnail) ? URL::to('/').'/uploads/media/' . $item->thumbnail : '',  
                    "description"   =>  $item->description,
                    "is_image"      => (int) $item->is_image,
                    "is_video"      => (int) $item->is_video,
                    'name'          => $this->nulltoBlank($item->user->name),
                    'email'         => $this->nulltoBlank($item->user->email),
                    'phone'         => $this->nulltoBlank($item->user->phone),
                    'viewCount'     => isset($item->views) ? count($item->views) : 0,
                    'view_count'     => isset($item->views) ? count($item->views) : 0,
                    'profile_pic'   => isset($item->user->profile_pic) ? URL::to('/').'/uploads/user/' . $item->user->profile_pic : '',
                    'is_read'       => 0
                ];
            }
            else
            {
                $response['read'][] = [
                    "post_id"       => (int) $item->id, 
                    "user_id"       => (int) $item->user_id,
                    "tag_user_id"   => (int) $item->tag_user_id,
                    "media"         =>  URL::to('/').'/uploads/media/' . $item->media, 
                    "thumbnail"     =>  isset($item->thumbnail) ? URL::to('/').'/uploads/media/' . $item->thumbnail : '',  
                    "description"   =>  $item->description,
                    "is_image"      => (int) $item->is_image,
                    "is_video"      => (int) $item->is_video,
                    'name'          => $this->nulltoBlank($item->user->name),
                    'email'         => $this->nulltoBlank($item->user->email),
                    'phone'         => $this->nulltoBlank($item->user->phone),
                    'viewCount'     => isset($item->views) ? count($item->views) : 0,
                    'view_count'     => isset($item->views) ? count($item->views) : 0,
                    'profile_pic'   => isset($item->user->profile_pic) ? URL::to('/').'/uploads/user/' . $item->user->profile_pic : '',
                    'is_read'       => 1
                ];
            }
        }

        return $response;
    }

    public function getMyPosts($user, $items)
    {
        $userReadPostIds    = [];
        $userReadPosts      = access()->getReadPostIds($user->id);

        if($userReadPosts)
        {   
            $userReadPostIds = array_values($userReadPosts->unique()->toArray());
        }

        $response = [];

        foreach($items as $item)
        {
            $item->user = (object)$item->user;
            $isRead     = in_array($item->id, $userReadPostIds) ? 1 :0;

            $response[] = [
                "post_id"       => (int) $item->id, 
                "user_id"       => (int) $item->user_id,
                "tag_user_id"   => (int) $item->tag_user_id,
                "media"         =>  URL::to('/').'/uploads/media/' . $item->media, 
                "thumbnail"     =>  isset($item->thumbnail) ? URL::to('/').'/uploads/media/' . $item->thumbnail : '',  
                "description"   =>  $item->description,
                "is_image"      => (int) $item->is_image,
                "is_video"      => (int) $item->is_video,
                'name'          => $this->nulltoBlank($item->user->name),
                'email'         => $this->nulltoBlank($item->user->email),
                'phone'         => $this->nulltoBlank($item->user->phone),
                'viewCount'     => isset($item->views) ? count($item->views) : 0,
                'view_count'    => isset($item->views) ? count($item->views) : 0,
                'profile_pic'   => isset($item->user->profile_pic) ? URL::to('/').'/uploads/user/' . $item->user->profile_pic : '',
                'is_read'       => $isRead
            ];
           
        }

        return $response;
    }

    public function singlePost($item)
    {
        $commentData        = [];
        $item->user         = (object)$item->user;
        $item->tag_user     = (object)$item->tag_user;

        $userName       = explode(' ', $item->user->name);
        $taggedUserName = explode(' ', $item->tag_user->name);

        $response = [
            "post_id"       => (int) $item->id, 
            "user_id"       => (int) $item->user_id,
            "tag_user_id"   => (int) $item->tag_user_id,
            "media"         =>  URL::to('/').'/uploads/media/' . $item->media,
            "thumbnail"     =>  isset($item->thumbnail) ? URL::to('/').'/uploads/media/' . $item->thumbnail : '',  
            "description"   =>  $item->description,
            "is_image"      => (int) $item->is_image,
            "is_video"      => (int) $item->is_video,
            'name'          => $this->nulltoBlank($item->user->name),
            'email'         => $this->nulltoBlank($item->user->email),
            'phone'         => $this->nulltoBlank($item->user->phone),
            'profile_pic'   => isset($item->user->profile_pic) ? URL::to('/').'/uploads/user/' . $item->user->profile_pic : '',
            'is_read'       => 1,
            'is_accepted'       => (int) $item->is_accepted,
            'post_user_name'     => $userName[0],
            'tagged_user_name'     => $taggedUserName[0],
            'viewCount'    => isset($item->views) ? count($item->views) : 0,
            'view_count'    => isset($item->views) ? count($item->views) : 0,
            'comments_count' => isset($item->comments) ? count($item->comments) : 0,
            'comments'      => []
        ];

        if(isset($item->comments) && count($item->comments))
        {
            $commentData = [];

            foreach($item->comments as $comment)
            {
                $commentData[] = [
                    'user_id'       => $comment->user_id,
                    'name'          => $comment->user->name,
                    'profile_pic'   => isset($comment->user->profile_pic) ? URL::to('/').'/uploads/user/' . $comment->user->profile_pic : '',
                    'comment'       => $comment->comment,
                    'comment_id'    => $comment->id
                ];
            }
        }

        $response['comments'] = $commentData;

        return $response;

    }
}