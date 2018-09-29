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
        $response       = [];
        $currentUserId  = access()->user()->id;
        $connectionIds  = access()->getMyConnectionIds($currentUserId);
        $requestIds     = access()->getMyRequestIds($currentUserId);

        if(isset($items) && count($items))
        {
            foreach($items as $item)
            {
                $isLoved        = 0;
                $isGroupFeed    = 0;
                $isLoved        = 0;
                $isMyLoved      = 0;
                $isMyLiked      = 0;
                $isCommented    = 0;
                $feedImages     = [];
                $feedGroup      = [];
                $feedCategory   = [];
                $feedLoveUsers  = [];
                $feedLikeUsers  = [];
                $feedComments   = [];
                $tagUsers       = [];
                $loveLikes      = [];
                $feedLoveLikeUsers = [];

                if(isset($item->feed_group) && count($item->feed_group))
                {
                    $isGroupFeed = 1;
                    $feedGroup = [
                        'group_id'      => (int) $item->feed_group->id,
                        'group_name'    => $item->feed_group->title
                    ];
                }

                if(isset($item->feed_category) && count($item->feed_category))
                {
                    $feedCategory = [
                        'category_id' => (int) $item->feed_category->id,
                        'title'       => $item->feed_category->title
                    ];                
                }

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
                        if($love->user->id == $currentUserId)
                        {
                            $isMyLoved  = 1;
                            $isLoved    = 1;
                        }

                        $feedLoveUsers[] = [
                            'user_id'       => (int)  $love->user->id,
                            'username'      => $love->user->name,
                            'is_love'       => 1,
                            'is_like'       => 0,
                            'profile_pic'   => URL::to('/').'/uploads/user/' . $love->user->profile_pic,
                            'created_at'    => date('m/d/Y H:i:s', strtotime($love->created_at))
                        ];
                    }
                }

                

                if(isset($item->feed_likes) && count($item->feed_likes))
                {
                    foreach($item->feed_likes as $like)
                    {
                        if($like->user->id == $currentUserId)
                        {
                            $isMyLiked = 1;
                            $isLiked = 1;
                        }

                        $feedLikeUsers[] = [
                            'user_id'       => (int)  $like->user->id,
                            'username'      => $like->user->name,
                            'is_like'       => 1,
                            'is_love'       => 0,
                            'profile_pic'   => URL::to('/').'/uploads/user/' . $like->user->profile_pic,
                            'created_at'    => date('m/d/Y H:i:s', strtotime($like->created_at))
                        ];
                    }
                }


                $loveLikeUsers = array_merge($feedLoveUsers, $feedLikeUsers);
                $loveLikeUsers = collect($loveLikeUsers);
                $loveLikeUsers = $loveLikeUsers->sortBy('created_at');
                $loveLikeIds   = [];

                if(isset($loveLikeUsers) && count($loveLikeUsers))
                {
                    foreach($loveLikeUsers as $loveLike)   
                    {

                        $loveLike = (object)$loveLike;

                        if(in_array($loveLike->user_id, $loveLikeIds))
                        {
                            continue;
                        }

                        $loveLikeIds[] = $loveLike->user_id;

                        $isLiked       = $loveLikeUsers->where('user_id', $loveLike->user_id)->where('is_like', 1)->first();
                        $isLoved       = $loveLikeUsers->where('user_id', $loveLike->user_id)->where('is_love', 1)->first();
                        
                        $loveFlag = $likeFlag = 0;

                        if(isset($isLoved['is_love']))
                        {
                            $loveFlag = $isLoved['is_love'];
                        }
                        if(isset($isLiked['is_like']))
                        {
                            $likeFlag = $isLiked['is_like'];
                        }

                        $isConnected = in_array($loveLike->user_id, $connectionIds) ? 1 : 0;
                        $isRequested = in_array($loveLike->user_id, $requestIds) ? 1 : 0;
                        $isMe  = $loveLike->user_id == $currentUserId ? 1 : 0;
                        $feedLoveLikeUsers[] = [
                            'user_id'       => (int) $loveLike->user_id,
                            'is_connected'  => $isConnected,
                            'is_requested'  => $isRequested,
                            'is_me'         => $isMe,
                            'username'      => $loveLike->username,
                            'is_like'       => $likeFlag,
                            'is_love'       => $loveFlag,
                            'profile_pic'   => $loveLike->profile_pic,
                            'created_at'    => $loveLike->created_at
                        ];
                    }
                }
                
                if(isset($item->feed_comments) && count($item->feed_comments))
                {
                    foreach($item->feed_comments as $comment)
                    {
                        if($comment->user->id == $currentUserId)
                            $isCommented = 1;

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
                    'create_at'     => date('m/d/Y h:i:s', strtotime($item->created_at)),
                    'isLiked'       => (int) $isMyLiked,
                    'isLoved'       => (int) $isMyLoved,
                    'isCommented'   => (int) $isCommented,
                    'likeCount'     => (int) count($item->feed_likes),
                    'loveCount'     => (int) count($item->feed_loves),
                    'commentCount'  => (int) count($item->feed_comments),
                    'feedCategory'  => (object) $feedCategory,
                    'is_group_feed' => $isGroupFeed,
                    'groupData'     => (object) $feedGroup,
                    'loveUsers'     => $feedLoveUsers,
                    'likeUsers'     => $feedLikeUsers,
                    'allComments'   => $feedComments,
                    'tagUsers'      => $tagUsers,
                    'loveLikeUsers' => $feedLoveLikeUsers
                ];
            }
        }
        return $response;
    }

    /**
     * Get Love Like
     * 
     * @param object $item
     * @return array
     */
    public function getLoveLike($item)
    {
       $response       = [];
       $currentUserId  = access()->user()->id;
       $connectionIds  = access()->getMyConnectionIds($currentUserId);
       $requestIds     = access()->getMyRequestIds($currentUserId);

        if(isset($item) && count($item))
        {
            $isLoved        = 0;
            $isLiked        = 0;
            $isCommented    = 0;
            $feedImages     = [];
            $feedLoveUsers  = [];
            $feedLikeUsers  = [];
            $feedComments   = [];
            $tagUsers       = [];
            $loveLikes      = [];
            $feedLoveLikeUsers = [];

            if(isset($item->feed_loves) && count($item->feed_loves))
            {
                foreach($item->feed_loves as $love)
                {
                    if($love->user->id == $currentUserId)
                        $isLoved = 1;

                    $feedLoveUsers[] = [
                        'user_id'       => (int)  $love->user->id,
                        'username'      => $love->user->name,
                        'is_love'       => 1,
                        'is_like'       => 0,
                        'profile_pic'   => URL::to('/').'/uploads/user/' . $love->user->profile_pic,
                        'created_at'    => date('m/d/Y H:i:s', strtotime($love->created_at))
                    ];
                }
            }

            if(isset($item->feed_likes) && count($item->feed_likes))
            {
                foreach($item->feed_likes as $like)
                {
                    if($like->user->id == $currentUserId)
                        $isLiked = 1;

                    $feedLikeUsers[] = [
                        'user_id'       => (int)  $like->user->id,
                        'username'      => $like->user->name,
                        'is_like'       => 1,
                        'is_love'       => 0,
                        'profile_pic'   => URL::to('/').'/uploads/user/' . $like->user->profile_pic,
                        'created_at'    => date('m/d/Y H:i:s', strtotime($like->created_at))
                    ];
                }
            }

            $loveLikeUsers = array_merge($feedLoveUsers, $feedLikeUsers);

            $loveLikeUsers = collect($loveLikeUsers);
            $loveLikeUsers = $loveLikeUsers->sortByDesc('created_at');
            $loveLikeIds   = [];

            if(isset($loveLikeUsers) && count($loveLikeUsers))
            {
                foreach($loveLikeUsers as $loveLike)   
                {

                    $loveLike = (object)$loveLike;

                    if(in_array($loveLike->user_id, $loveLikeIds))
                    {
                        continue;
                    }

                    $loveLikeIds[] = $loveLike->user_id;

                    $isLiked       = $loveLikeUsers->where('user_id', $loveLike->user_id)->where('is_like', 1)->first();
                    $isLoved       = $loveLikeUsers->where('user_id', $loveLike->user_id)->where('is_love', 1)->first();
                    
                    $loveFlag = $likeFlag = 0;

                    if(isset($isLoved['is_love']))
                    {
                        $loveFlag = $isLoved['is_love'];
                    }
                    if(isset($isLiked['is_like']))
                    {
                        $likeFlag = $isLiked['is_like'];
                    }

                    $isConnected = in_array($loveLike->user_id, $connectionIds) ? 1 : 0;
                    $isRequested = in_array($loveLike->user_id, $requestIds) ? 1 : 0;
                    $isMe  = $loveLike->user_id == $currentUserId ? 1 : 0;
                    $feedLoveLikeUsers[] = [
                        'user_id'       => (int) $loveLike->user_id,
                        'is_connected'  => $isConnected,
                        'is_requested'  => $isRequested,
                        'is_me'         => $isMe,
                        'username'      => $loveLike->username,
                        'is_like'       => $likeFlag,
                        'is_love'       => $loveFlag,
                        'profile_pic'   => $loveLike->profile_pic,
                        'created_at'    => $loveLike->created_at
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
                'create_at'     => date('m/d/Y h:i:s', strtotime($item->created_at)),
                'loveLikeUsers'=> $feedLoveLikeUsers
            ];
        }
        
        return $response; 
    }

    public function showSingleFeed($item)
    {
       $response       = [];
       $currentUserId  = access()->user()->id;
       $connectionIds  = access()->getMyConnectionIds($currentUserId);
       $requestIds     = access()->getMyRequestIds($currentUserId);

        if(isset($item) && count($item))
        {
            $isGroupFeed    = 0;
            $isLoved        = 0;
            $isLiked        = 0;
            $isMyLiked      = 0;
            $isMyLoved      = 0;
            $isCommented    = 0;
            $feedImages     = [];
            $feedLoveUsers  = [];
            $feedLikeUsers  = [];
            $feedComments   = [];
            $tagUsers       = [];
            $loveLikes      = [];
            $feedGroup      = [];
            $feedCategory   = [];
            $feedLoveLikeUsers = [];

            if(isset($item->feed_group) && count($item->feed_group))
            {
                $isGroupFeed    = 1;
                $feedGroup      = [
                    'group_id'      => (int) $item->feed_group->id,
                    'group_name'    => $item->feed_group->title
                ];
            }

            if(isset($item->feed_category) && count($item->feed_category))
            {
                $feedCategory = [
                    'category_id' => (int) $item->feed_category->id,
                    'title'       => $item->feed_category->title
                ];                
            }

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
                    if($love->user->id == $currentUserId)
                    {
                        $isMyLoved  = 1;
                        $isLoved    = 1;
                    }

                    $feedLoveUsers[] = [
                        'user_id'       => (int)  $love->user->id,
                        'username'      => $love->user->name,
                        'is_love'       => 1,
                        'is_like'       => 0,
                        'profile_pic'   => URL::to('/').'/uploads/user/' . $love->user->profile_pic,
                        'created_at'    => date('m/d/Y H:i:s', strtotime($love->created_at))
                    ];
                }
            }

            if(isset($item->feed_likes) && count($item->feed_likes))
            {
                foreach($item->feed_likes as $like)
                {
                    if($like->user->id == $currentUserId)
                    {
                        $isMyLiked  = 1;
                        $isLiked    = 1;
                    }

                    $feedLikeUsers[] = [
                        'user_id'       => (int)  $like->user->id,
                        'username'      => $like->user->name,
                        'is_like'       => 1,
                        'is_love'       => 0,
                        'profile_pic'   => URL::to('/').'/uploads/user/' . $like->user->profile_pic,
                        'created_at'    => date('m/d/Y H:i:s', strtotime($like->created_at))
                    ];
                }
            }

            $loveLikeUsers = array_merge($feedLoveUsers, $feedLikeUsers);
            $loveLikeUsers = collect($loveLikeUsers);

            if(isset($loveLikeUsers) && count($loveLikeUsers))
            {
                $loveLikeUsers = $loveLikeUsers->sortByDesc('created_at');
            }
            $loveLikeIds   = [];

            if(isset($loveLikeUsers) && count($loveLikeUsers))
            {
                foreach($loveLikeUsers as $loveLike)   
                {

                    $loveLike = (object)$loveLike;

                    if(in_array($loveLike->user_id, $loveLikeIds))
                    {
                        continue;
                    }

                    $loveLikeIds[] = $loveLike->user_id;

                    $isLiked       = $loveLikeUsers->where('user_id', $loveLike->user_id)->where('is_like', 1)->first();
                    $isLoved       = $loveLikeUsers->where('user_id', $loveLike->user_id)->where('is_love', 1)->first();
                    
                    $loveFlag = $likeFlag = 0;

                    if(isset($isLoved['is_love']))
                    {
                        $loveFlag = $isLoved['is_love'];
                    }
                    if(isset($isLiked['is_like']))
                    {
                        $likeFlag = $isLiked['is_like'];
                    }

                    $isConnected = in_array($loveLike->user_id, $connectionIds) ? 1 : 0;
                    $isRequested = in_array($loveLike->user_id, $requestIds) ? 1 : 0;
                    $isMe  = $loveLike->user_id == $currentUserId ? 1 : 0;
                    $feedLoveLikeUsers[] = [
                        'user_id'       => (int) $loveLike->user_id,
                        'is_connected'  => $isConnected,
                        'is_requested'  => $isRequested,
                        'is_me'         => $isMe,
                        'username'      => $loveLike->username,
                        'is_like'       => $likeFlag,
                        'is_love'       => $loveFlag,
                        'profile_pic'   => $loveLike->profile_pic,
                        'created_at'    => $loveLike->created_at
                    ];
                }
            }

            if(isset($item->feed_comments) && count($item->feed_comments))
            {
                foreach($item->feed_comments as $comment)
                {
                    if($comment->user->id == $currentUserId)
                        $isCommented = 1;

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
                'create_at'     => date('m/d/Y h:i:s', strtotime($item->created_at)),
                'isLiked'       => (int) $isMyLiked,
                'isLoved'       => (int) $isMyLoved,
                'isCommented'   => (int) $isCommented,
                'feed_images'   => $feedImages,
                'likeCount'     => (int) count($item->feed_likes),
                'loveCount'     => (int) count($item->feed_loves),
                'commentCount'  => (int) count($item->feed_comments),
                'tagUserCount'  => (int) count($item->feed_tag_users),
                'loveUsers'     => $feedLoveUsers,
                'feedCategory'  => (object) $feedCategory,
                'is_group_feed' => $isGroupFeed,
                'groupData'     => (object) $feedGroup,
                'likeUsers'     => $feedLikeUsers,
                'allComments'   => $feedComments,
                'tagUsers'      => $tagUsers,
                'loveLikeUsers'=> $feedLoveLikeUsers
            ];
        }
        
        return $response; 
    }
}