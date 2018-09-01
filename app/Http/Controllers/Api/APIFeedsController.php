<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\FeedsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Feeds\EloquentFeedsRepository;
use App\Models\UserGroups\UserGroups;
use App\Models\Connections\Connections;

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
        $offset     = $request->has('offset') ? $request->get('offset') : 0;
        $perPage    = $request->has('per_page') ? $request->get('per_page') : 100;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $items      = $this->repository->model->with([
            'user', 'feed_category', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user', 'feed_tag_users', 'feed_tag_users.user'
        ])
        ->offset($offset)
        ->limit($perPage)
        ->get();

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
    public function myTextFeeds(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $offset     = $request->has('offset') ? $request->get('offset') : 0;
        $perPage    = $request->has('per_page') ? $request->get('per_page') : 100;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $items      = $this->repository->model->with([
            'user', 'feed_category', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user', 'feed_tag_users', 'feed_tag_users.user'
        ])
        ->where('feed_type', 1)
        ->where('user_id', $userInfo->id)
        ->offset($offset)
        ->limit($perPage)
        ->get();

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
    public function myImageFeeds(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $offset     = $request->has('offset') ? $request->get('offset') : 0;
        $perPage    = $request->has('per_page') ? $request->get('per_page') : 100;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $items      = $this->repository->model->with([
            'user', 'feed_category', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user', 'feed_tag_users', 'feed_tag_users.user'
        ])
        ->where('feed_type', 2)
        ->where('user_id', $userInfo->id)
        ->offset($offset)
        ->limit($perPage)
        ->get();

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
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $model = $this->repository->create($request->all());

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
                'message' => 'Feed Created successfully'
            ];

            return $this->successResponse($responseData, 'Feed Created Successfully');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
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
                'user', 'feed_category', 'feed_images', 'feed_loves', 'feed_loves.user', 'feed_likes', 'feed_likes.user', 'feed_comments', 'feed_comments.user'
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
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->update($itemId, $request->all());

            if($status)
            {
                $itemData       = $this->repository->getById($itemId);
                $responseData   = $this->feedsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Feeds is Edited Successfully');
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
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->destroy($itemId);

            if($status)
            {
                return $this->successResponse([
                    'success' => 'Feeds Deleted'
                ], 'Feeds is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}