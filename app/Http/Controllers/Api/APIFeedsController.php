<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\FeedsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Feeds\EloquentFeedsRepository;
use App\Models\UserGroups\UserGroups;
use App\Models\Connections\Connections;
use App\Models\FeedTagUsers\FeedTagUsers;
use App\Models\Access\User\User;
use App\Models\FeedNotifications\FeedNotifications;

class APIFeedsController extends BaseApiController
{
    /**
     * Feeds Transformer
     *
     * @var Object
     */
    protected $feedsTransformer;

    /**
     * Repository
     *
     * @var Object
     */
    protected $repository;

    /**
     * PrimaryKey
     *
     * @var string
     */
    protected $primaryKey = 'feedsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentFeedsRepository();
        $this->feedsTransformer = new FeedsTransformer();
    }

    /**
     * List of All Feeds
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $blockFeeds = $userInfo->feeds_reported()->pluck('feed_id')->toArray();
        $offset     = $request->has('offset') ? $request->get('offset') : 0;
        $perPage    = $request->has('per_page') ? $request->get('per_page') : 100;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';


        $newOffset = $offset * $perPage;

        $items      = $this->repository->model->with([
            'user', 'feed_category', 'feed_group', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user', 'feed_tag_users', 'feed_tag_users.user'
        ])
        ->where('is_individual', 0)
        ->whereNotIn('id', $blockFeeds)
        /*->offset($offset)
        ->limit($perPage)*/
        ->skip($newOffset)
        ->take($perPage)
        ->orderBy('id', 'DESC')
        ->get();

        if($offset == 0)
        {
            $skipp = $perPage;
        }
        else
        {
            $skipp = ($offset + 1) * $perPage;
        }
        
        $itemCount      = $this->repository->model->where('is_individual', 0)
        ->whereNotIn('id', $blockFeeds)
        ->skip($skipp)
        ->take(1)
        ->orderBy('id', 'DESC')
        ->get();

        if(isset($items) && count($items))
        {
            $loadMore    = 0;
            $itemsOutput = $this->feedsTransformer->showAllFeeds($items);

            if(isset($itemCount) && count($itemCount) > 0)
            {
                $loadMore = 1;
            }

            return $this->successResponseWithPagination($itemsOutput, '', $loadMore);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Feeds!'
            ], 'No Feeds Found !');
    }

    /**
     * List of All Feeds
     *
     * @param Request $request
     * @return json
     */
    public function refreshFeeds(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $blockFeeds = $userInfo->feeds_reported()->pluck('feed_id')->toArray();
        $feedId     = $request->has('feed_id') ? $request->get('feed_id') : false;

        $query     = $this->repository->model->with([
            'user', 'feed_category', 'feed_group', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user', 'feed_tag_users', 'feed_tag_users.user'
        ])
        ->orderBy('id', 'DESC');

        if($feedId)
        {
           $query->where('id', '>=', $feedId);
        }
        $items = $query->whereNotIn('id', $blockFeeds)->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->feedsTransformer->showAllFeeds($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Feeds!'
            ], 'No Feeds Found !');
    }

    /**
     * List of All Feeds
     *
     * @param Request $request
     * @return json
     */
    public function filter(Request $request)
    {
        $keyword    = $request->has('keyword') ? $request->get('keyword') : false;
        $userInfo   = $this->getAuthenticatedUser();
        $blockFeeds = $userInfo->feeds_reported()->pluck('feed_id')->toArray();
        $offset     = $request->has('offset') ? $request->get('offset') : 0;
        $perPage    = $request->has('per_page') ? $request->get('per_page') : 100;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $query      = $this->repository->model->with([
            'user', 'feed_category', 'feed_group', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user', 'feed_tag_users', 'feed_tag_users.user'
        ])->whereNotIn('id', $blockFeeds);

        if($keyword)
        {
            $query->where('description', 'LIKE', "%$keyword%");
            $query->orWhereHas('user', function($q) use($keyword)
            {
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }

        $query = $query->offset($offset)
        ->limit($perPage)
        ->orderBy('id', 'DESC');

        $items = $query->get();
        if(isset($items) && count($items))
        {
            $itemsOutput = $this->feedsTransformer->showAllFeeds($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Feeds!'
            ], 'No Feeds Found !');
    }

    /**
     * Get Love Like
     * 
     * @param Request $request
     * @return json
     */
    public function getLoveLike(Request $request)
    {
        if($request->has('feed_id'))
        {
            $item      = $this->repository->model->with([
                'user', 'feed_category', 'feed_group', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user', 'feed_tag_users', 'feed_tag_users.user'
            ])
            ->where('id', $request->get('feed_id'))
            ->first();
            
            if(isset($item) && count($item))
            {
                $itemsOutput = $this->feedsTransformer->getLoveLike($item);

                return $this->successResponse($itemsOutput);
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'No Results found!'
            ], 'No Love Like Found!');  
    }

    /**
     * List of All Feeds
     *
     * @param Request $request
     * @return json
     */
    public function myTextFeeds(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $tagFeedIds = $userInfo->user_tag_feeds->pluck('feed_id')->toArray();
        $blockFeeds = $userInfo->feeds_reported()->pluck('feed_id')->toArray();
        $offset     = $request->has('offset') ? $request->get('offset') : 0;
        $perPage    = $request->has('per_page') ? $request->get('per_page') : 100;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $items      = $this->repository->model->with([
            'user', 'feed_category', 'feed_group', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user', 'feed_tag_users', 'feed_tag_users.user'
        ])
        ->whereNotIn('id', $blockFeeds)
        ->where('feed_type', 1)
        ->where('user_id', $userInfo->id)
        ->orWhereHas('feed_tag_users', function($q) use($userInfo)
        {
            $q->where('user_id', $userInfo->id);
        })
        ->orWhereIn('id', $tagFeedIds)
        ->orderBy('id', 'DESC')
        ->offset($offset)
        ->limit($perPage)
        ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->feedsTransformer->showMyFeeds($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Feeds!'
            ], 'No Feeds Found !');
    }

    /**
     * List of All Feeds
     *
     * @param Request $request
     * @return json
     */
    public function myImageFeeds(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $tagFeedIds = $userInfo->user_tag_feeds->pluck('feed_id')->toArray();
        $blockFeeds = $userInfo->feeds_reported()->pluck('feed_id')->toArray();
        $offset     = $request->has('offset') ? $request->get('offset') : 0;
        $perPage    = $request->has('per_page') ? $request->get('per_page') : 100;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $items      = $this->repository->model->with([
            'user', 'feed_category', 'feed_group', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user', 'feed_tag_users', 'feed_tag_users.user'
        ])
        ->whereNotIn('id', $blockFeeds)
        ->where('feed_type', 2)
        ->where('user_id', $userInfo->id)
        ->orWhereIn('id', $tagFeedIds)
        ->orderBy('id', 'DESC')
        ->offset($offset)
        ->limit($perPage)
        ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->feedsTransformer->showMyFeeds($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Feeds!'
            ], 'No Feeds Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $model      = $this->repository->create($request->all());
        $userInfo   = $this->getAuthenticatedUser();

        if($model)
        {
            $input      = $request->all();
            $tagUsers   = [];

            if(isset($input['feed_images']) && count($input['feed_images']))
            {
                $feedImages = [];

                foreach($input['feed_images'] as $image)
                {
                    $imageName  = rand(11111, 99999) . '_feed.' . $image->getClientOriginalExtension();

                    $image->move(base_path() . '/public/uploads/feeds/', $imageName);

                    $feedImages[] = [
                        'feed_id' => $model->id,
                        'image'   => $imageName 
                    ];
                }

                if(count($feedImages))
                {
                    $model->feed_images()->insert($feedImages);
                }
            }


            if(isset($input['tag_users']))
            {
                $tagUsers       = explode(',', $input['tag_users']);
                $tagUserData    = [];
                foreach($tagUsers as $tagUser)
                {
                    $tagUserData[] = [
                        'user_id'   => $tagUser,
                        'feed_id'   => $model->id
                    ];
                }

                if(count($tagUserData))
                {
                    $model->feed_tag_users()->insert($tagUserData);
                }

                $allMembers = User::whereIn('id', $tagUsers)->get();

                foreach($allMembers as $tagMember)
                {
                    $text       = $userInfo->name . ' tagged you in a post.';
                    $payload    = [
                        'mtitle'            => '',
                        'mdesc'             => $text,
                        'user_id'           => $tagMember->id,
                        'feed_id'           => $model->id,
                        'feed_type'         => $model->type,
                        'badgeCount'        => access()->getUnreadNotificationCount($tagMember->id),
                        'mtype'             => 'TAG_USER'
                    ];
                    $storeNotification = [
                        'user_id'           => $tagMember->id,
                        'from_user_id'      => $userInfo->id,
                        'description'       => $text,
                        'icon'              => 'TAG_USER.png',
                        'notification_type' => 'TAG_USER'
                    ];

                    access()->addNotification($storeNotification);
                    access()->sentPushNotification($tagMember, $payload);
                }
            }

            if(isset($input['group_id']))
            {
                $userInfo           = $this->getAuthenticatedUser();
                $groupMemberData    = [];
                $userGroup          = UserGroups::where([
                    // 'user_id'   => $userInfo->id,
                    'id'        => $input['group_id']
                ])
                ->with('group_members')
                ->first();

                $uniqueGrpMembers = [];

                if(isset($userGroup) && isset($userGroup->group_members))
                {
                    foreach($userGroup->group_members as $member)
                    {
                        if(in_array($member->member_id, $tagUsers))
                        {
                            continue;
                        }

                        if(in_array($member->member_id, $uniqueGrpMembers))
                        {
                            continue;
                        }
                        
                        $uniqueGrpMembers[] = $member->member_id;

                        $groupMemberData[] = [
                            'group_id'  => $userGroup->id,
                            'user_id'   => $member->member_id,
                            'feed_id'   => $model->id
                        ];
                    }

                    if(count($groupMemberData))
                    {
                        $model->feed_tag_users()->insert($groupMemberData);
                    }
                }

                $allGroupMembers = User::whereIn('id', $uniqueGrpMembers)->get();

                foreach($allGroupMembers as $tagGroupMember)
                {
                    $text       = $userInfo->name . ' tagged your group.';
                    $payload    = [
                        'mtitle'            => '',
                        'mdesc'             => $text,
                        'user_id'           => $tagGroupMember->id,
                        'feed_id'           => $model->id,
                        'feed_type'         => $model->type,
                        'badgeCount'        => access()->getUnreadNotificationCount($tagGroupMember->id),
                        'mtype'             => 'TAG_GROUP_USER'
                    ];
                    $storeNotification = [
                        'user_id'           => $tagGroupMember->id,
                        'from_user_id'      => $userInfo->id,
                        'description'       => $text,
                        'icon'              => 'TAG_GROUP_USER.png',
                        'notification_type' => 'TAG_GROUP_USER'
                    ];

                    access()->addNotification($storeNotification);
                    access()->sentPushNotification($tagGroupMember, $payload);
                }

            }

            $responseData = [
                'message' => 'Feed Created successfully'
            ];

            return $this->successResponse($responseData, 'Feed Created Successfully');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * Update Feed Category
     *
     * @param Request $request
     * @return string
     */
    public function updateCategory(Request $request)
    {
        if($request->has('feed_id') && $request->has('category_id') )
        {
            $feed = $this->repository->model->where([
                'id' => $request->get('feed_id')
            ])->first();


            if(isset($feed) && isset($feed->id))
            {
                $feed->category_id = $request->get('category_id');  

                if($feed->save())
                {
                    $responseData = [
                        'message' => 'Feed category updated successfully'
                    ];

                    return $this->successResponse($responseData, 'Feed category updated successfully');
                }
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Feed Id or No Feed Found !'
            ], 'Invalid Feed Id or No Feed Found !');
    }

    /**
     * View
     *
     * @param Request $request
     * @return string
     */
    public function show(Request $request)
    {
        if($request->has('feed_id'))
        {
            $item = $this->repository->model->with([
                'user', 'feed_category', 'feed_group', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user'
            ])
            ->where('id', $request->get('feed_id'))
            ->first();

            if(isset($item) && count($item))
            {
                $itemsOutput = $this->feedsTransformer->showSingleFeed($item);

                return $this->successResponse($itemsOutput);
            }
        }


        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or Item not exists !'
            ], 'Something went wrong !');
    }

    /**
     * Edit
     *
     * @param Request $request
     * @return string
     */
    public function edit(Request $request)
    {
        if($request->has('feed_id'))
        {
            $input      = $request->all();
            $userInfo   = $this->getAuthenticatedUser();
            $model      = $this->repository->model->where([
                'id'        => $request->get('feed_id'),
                'user_id'   => $userInfo->id
            ])->first();
            $tagUsers   = [];
            
            if(isset($model->id))
            {
                $model->update($input);
                //$model->feed_images()->delete();
                $model->feed_tag_users()->delete();

                /*if(isset($input['feed_images']) && count($input['feed_images']))
                {
                    $feedImages = [];

                    foreach($input['feed_images'] as $image)
                    {
                        $imageName  = rand(11111, 99999) . '_feed.' . $image->getClientOriginalExtension();

                        $image->move(base_path() . '/public/uploads/feeds/', $imageName);

                        $feedImages[] = [
                            'feed_id' => $model->id,
                            'image'   => $imageName 
                        ];
                    }

                    if(count($feedImages))
                    {
                        $model->feed_images()->insert($feedImages);
                    }
                }*/


                if(isset($input['tag_users']))
                {
                    $tagUsers       = explode(',', $input['tag_users']);
                    $tagUserData    = [];
                    foreach($tagUsers as $tagUser)
                    {
                        $tagUserData[] = [
                            'user_id'   => $tagUser,
                            'feed_id'   => $model->id
                        ];
                    }

                    if(count($tagUserData))
                    {
                        $model->feed_tag_users()->insert($tagUserData);
                    }
                }

                if(isset($input['group_id']))
                {
                    $userInfo           = $this->getAuthenticatedUser();
                    $groupMemberData    = [];
                    $userGroup          = UserGroups::where([
                        'user_id'   => $userInfo->id,
                        'id'        => $input['group_id']
                    ])
                    ->with('group_members')
                    ->first();

                    $uniqueGrpMembers = [];

                    if(isset($userGroup) && isset($userGroup->group_members))
                    {
                        foreach($userGroup->group_members as $member)
                        {
                            if(in_array($member->member_id, $tagUsers))
                            {
                                continue;
                            }

                            if(in_array($member->member_id, $uniqueGrpMembers))
                            {
                                continue;
                            }
                            
                            $uniqueGrpMembers[] = $member->member_id;

                            $groupMemberData[] = [
                                'user_id'   => $member->member_id,
                                'feed_id'   => $model->id
                            ];
                        }

                        if(count($groupMemberData))
                        {
                            $model->feed_tag_users()->insert($groupMemberData);
                        }
                    }
                }

                $responseData = [
                    'message' => 'Feed Updated successfully'
                ];
                
                return $this->successResponse($responseData, 'Feed Updated Successfully');
            }
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Delete
     *
     * @param Request $request
     * @return string
     */
    public function delete(Request $request)
    {
        if($request->has('feed_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $model      = $this->repository->model->where([
                'id'        => $request->get('feed_id'),
                'user_id'   => $userInfo->id
            ])->first();

            if(isset($model) && isset($model->id))
            {
                if($model->delete())
                {
                    FeedNotifications::where('feed_id', $request->get('feed_id'))->delete();

                    return $this->successResponse([
                        'success' => 'Feed Deleted successfully'
                    ], 'Feed Deleted Successfully');
                }
            }
        }
        
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Input'
        ], 'No feed found');
    }

    /**
     * Un Tag
     * 
     * @param Request $request
     * @return json
     */
    public function unTag(Request $request)
    {
        if($request->has('feed_id'))
        {
            $feed       = $this->repository->model->where('id', $request->get('feed_id'))->first();
            $userInfo   = $this->getAuthenticatedUser();

            if(isset($feed))
            {
                $status = FeedTagUsers::where([
                    'feed_id' => $request->get('feed_id'),
                    'user_id' => $userInfo->id
                ])->delete();

                if($status)
                {
                    $responseData = [
                        'message' => 'Untag user successfully'
                    ];
                    
                    return $this->successResponse($responseData, 'Untag user successfully');
                }
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Input'
        ], 'No feed found');
    }
}