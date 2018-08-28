<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

class FeedsTransformer extends Transformer
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
            "feedsId" => (int) $item->id, "feedsUserId" =>  $item->user_id, "feedsCategoryId" =>  $item->category_id, "feedsFeedType" =>  $item->feed_type, "feedsDescription" =>  $item->description, "feedsCreatedAt" =>  $item->created_at, "feedsUpdatedAt" =>  $item->updated_at, 
        ];
    }

    /**
     * Show All Feeds
     * 
     * @param object $items
     * @return array
     */
    public function showAllFeeds($items)
    {
        $response = [];

        if(isset($items) && count($items))
        {
            foreach($items as $item)
            {
                $feedImages     = [];
                $feedLoveUsers  = [];
                $feedLikeUsers  = [];
                $feedComments   = [];

                if(isset($item->feed_loves) && count($item->feed_loves))
                {
                    foreach($item->feed_loves as $love)
                    {
                        $feedLoveUsers[] = [
                            'user_id'       => (int)  $love->user->id,
                            'username'      => $love->user->name,
                            'profile_pic'   => URL::to('/').'/uploads/user/' . $love->user->profile_pic,
                        ];
                    }
                }

                if(isset($item->feed_likes) && count($item->feed_likes))
                {
                    foreach($item->feed_likes as $like)
                    {
                        $feedLikeUsers[] = [
                            'user_id'       => (int)  $like->user->id,
                            'username'      => $like->user->name,
                            'profile_pic'   => URL::to('/').'/uploads/user/' . $like->user->profile_pic,
                        ];
                    }
                }

                if(isset($item->feed_comments) && count($item->feed_comments))
                {
                    foreach($item->feed_comments as $comment)
                    {
                        $feedComments[] = [
                            'comment_id' => (int) $comment->id,
                            'feed_id'    => (int) $comment->feed_id,
                            'user_id'    => (int) $comment->user_id,
                            'comment'    => $comment->comment,
                            'username'   => $comment->user->name,
                            'profile_pic'   =>  URL::to('/').'/uploads/user/' . $comment->user->profile_pic,
                            'create_at'  => date('m/d/Y h:i:s', strtotime($comment->created_at))
                        ];
                    }
                }


                if(isset($item->feed_images) && count($item->feed_images))
                {
                    foreach($item->feed_images as $image)
                    {
                        $feedImages[] = [
                            'feed_image_id' => (int) $image->id,
                            'feed_image'    => URL::to('/').'/uploads/feeds/' . $image->image
                        ];
                    }
                }

                $response[] = [
                    'feed_id'       => (int) $item->id,
                    'feed_type'     => $item->feed_type,
                    'user_id'       => (int)  $item->user_id,
                    'username'      => $item->user->name,
                    'profile_pic'   => URL::to('/').'/uploads/user/' . $item->user->profile_pic,
                    'description'   => $item->description,
                    'feed_images'   => $feedImages,
                    'likeCount'     => (int) count($item->feed_likes),
                    'loveCount'     => (int) count($item->feed_loves),
                    'commentCount'  => (int) count($item->feed_comments),
                    'loveUsers'     => $feedLoveUsers,
                    'likeUsers'     => $feedLikeUsers,
                    'allComments'   => $feedComments
                ];
            }
        }
        return $response;
    }

    public function showSingleFeed($item)
    {
       $response = [];

        if(isset($item) && count($item))
        {
            
            $feedImages     = [];
            $feedLoveUsers  = [];
            $feedLikeUsers  = [];
            $feedComments   = [];
            $tagUsers       = [];

            if(isset($item->feed_tag_users) && count($item->feed_tag_users))
            {
                foreach($item->feed_tag_users as $tagUser)
                {
                    $tagUsers[] = [
                        'user_id'       => (int)  $tagUser->user->id,
                        'username'      => $tagUser->user->name,
                        'profile_pic'   => URL::to('/').'/uploads/user/' . $tagUser->user->profile_pic,
                    ];
                }
            }

            if(isset($item->feed_loves) && count($item->feed_loves))
            {
                foreach($item->feed_loves as $love)
                {
                    $feedLoveUsers[] = [
                        'user_id'       => (int)  $love->user->id,
                        'username'      => $love->user->name,
                        'profile_pic'   => URL::to('/').'/uploads/user/' . $love->user->profile_pic,
                    ];
                }
            }

            if(isset($item->feed_likes) && count($item->feed_likes))
            {
                foreach($item->feed_likes as $like)
                {
                    $feedLikeUsers[] = [
                        'user_id'       => (int)  $like->user->id,
                        'username'      => $like->user->name,
                        'profile_pic'   => URL::to('/').'/uploads/user/' . $like->user->profile_pic,
                    ];
                }
            }

            if(isset($item->feed_comments) && count($item->feed_comments))
            {
                foreach($item->feed_comments as $comment)
                {
                    $feedComments[] = [
                        'comment_id' => (int) $comment->id,
                        'feed_id'    => (int) $comment->feed_id,
                        'user_id'    => (int) $comment->user_id,
                        'comment'    => $comment->comment,
                        'username'   => $comment->user->name,
                        'profile_pic'   =>  URL::to('/').'/uploads/user/' . $comment->user->profile_pic,
                        'create_at'  => date('m/d/Y h:i:s', strtotime($comment->created_at))
                    ];
                }
            }


            if(isset($item->feed_images) && count($item->feed_images))
            {
                foreach($item->feed_images as $image)
                {
                    $feedImages[] = [
                        'feed_image_id' => (int) $image->id,
                        'feed_image'    => URL::to('/').'/uploads/feeds/' . $image->image
                    ];
                }
            }

            $response = [
                'feed_id'       => (int) $item->id,
                'feed_type'     => $item->feed_type,
                'user_id'       => (int)  $item->user_id,
                'username'      => $item->user->name,
                'profile_pic'   => URL::to('/').'/uploads/user/' . $item->user->profile_pic,
                'description'   => $item->description,
                'feed_images'   => $feedImages,
                'likeCount'     => (int) count($item->feed_likes),
                'loveCount'     => (int) count($item->feed_loves),
                'commentCount'  => (int) count($item->feed_comments),
                'tagUserCount'  => (int) count($item->feed_tag_users),
                'loveUsers'     => $feedLoveUsers,
                'likeUsers'     => $feedLikeUsers,
                'allComments'   => $feedComments,
                'tagUsers'      => $tagUsers
            ];
        }
        
        return $response; 
    }
}