<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\CommentsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Comments\EloquentCommentsRepository;
use App\Models\ReportComments\ReportComments;
use App\Models\Feeds\Feeds;
use App\Library\Push\PushNotification;
use App\Models\Notifications\Notifications;
use URL;

class APICommentsController extends BaseApiController
{
    /**
     * Comments Transformer
     *
     * @var Object
     */
    protected $commentsTransformer;

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
    protected $primaryKey = 'commentsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentCommentsRepository();
        $this->commentsTransformer = new CommentsTransformer();
    }

    /**
     * List of All Comments
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $paginate   = $request->get('paginate') ? $request->get('paginate') : false;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'ASC';
        $items      = $paginate ? $this->repository->model->orderBy($orderBy, $order)->paginate($paginate)->items() : $this->repository->getAll($orderBy, $order);

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->commentsTransformer->transformCollection($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Comments!'
            ], 'No Comments Found !');
    }

    /**
     * List of All Comments
     *
     * @param Request $request
     * @return json
     */
    public function getList(Request $request)
    {
        if($request->has('feed_id'))
        {
            $items = $this->repository->model->with([
                'user'
            ])
            ->where([
                'feed_id' => $request->get('feed_id') 
            ])
            ->orderBy('id')
            ->get();
        }
        
        if(isset($items) && count($items))
        {
            $itemsOutput = $this->commentsTransformer->transformFeedComments($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Comments!'
            ], 'No Comments Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        if($request->has('feed_id'))
        {
            $userInfo  = $this->getAuthenticatedUser();
            $feedInfo  = Feeds::with(['feed_tag_users', 'feed_tag_users.user', 'user'])->where('id', $request->get('feed_id'))->first();
            $model      = $this->repository->model->create([
                'user_id'       => (int) $userInfo->id,
                'feed_id'       => (int) $request->get('feed_id'),
                'comment'       => $request->get('comment')
            ]);

            if($model)
            {
                if($userInfo->id != $feedInfo->user->id)
                {
                    $text = $userInfo->name . ' has commented on your Feed.';
                    $payload = [
                        'mtitle'            => '',
                        'mdesc'             => $text,
                        'feed_id'           => $request->get('feed_id'),
                        'to_user_id'        => $feedInfo->user->id,
                        'feed_type'         => $feedInfo->feed_type,
                        'from_user_id'      => $userInfo->id,
                        'mtype'             => 'NEW_COMMENT'
                    ];

                    $storeNotification = [
                        'user_id'           => $feedInfo->user->id,
                        'from_user_id'      => $userInfo->id,
                        'description'       => $text,
                        'feed_id'           => $request->get('feed_id'),
                        'notification_type' => 'NEW_COMMENT'
                    ];

                    access()->addNotification($storeNotification);
                    access()->sentPushNotification($feedInfo->user, $payload);
                }

                if(isset($feedInfo->feed_tag_users) && count($feedInfo->feed_tag_users))
                {
                    $text = $userInfo->name . ' commented on a post you are tagged in.';
                    foreach($feedInfo->feed_tag_users as $tagUser)
                    {
                        if($userInfo->id == $tagUser->user->id)
                        {
                            continue;
                        }

                        $payload = [
                            'mtitle'            => '',
                            'mdesc'             => $text,
                            'feed_id'           => $request->get('feed_id'),
                            'to_user_id'        => $tagUser->user->id,
                            'feed_type'         => $feedInfo->feed_type,
                            'from_user_id'      => $userInfo->id,
                            'mtype'             => 'NEW_COMMENT_TAG_USERS'
                        ];

                        $storeNotification = [
                            'user_id'           => $tagUser->user->id,
                            'from_user_id'      => $userInfo->id,
                            'description'       => $text,
                            'feed_id'           => $request->get('feed_id'),
                            'notification_type' => 'NEW_COMMENT_TAG_USERS'
                        ];

                        access()->addNotification($storeNotification);
                        access()->sentPushNotification($tagUser->user, $payload);
                    }
                }
                
                $response = [
                    'comment_id' => $model->id,
                    'feed_id'    => $model->feed_id,
                    'user_id'    => $userInfo->id,
                    'comment'    => $request->get('comment'),
                    'username'   => $model->user->name,
                    'profile_pic'   =>  URL::to('/').'/uploads/user/' . $userInfo->profile_pic,
                    'create_at'  => date('m/d/Y h:i:s', strtotime($model->created_at))
                ];

                return $this->successResponse($response, 'Comments is Created Successfully');
            }
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function sendcomment(Request $request)
    {
        if($request->has('feed_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $feedInfo   = Feeds::with(['feed_tag_users', 'feed_tag_users.user', 'user'])->where('id', $request->get('feed_id'))->first();
            $model      = $this->repository->model->create([
                'user_id'       => $userInfo->id,
                'feed_id'       => $request->get('feed_id'),
                'comment'       => $request->get('comment')
            ]);

            if($model)
            {
                if($userInfo->id != $feedInfo->user->id)
                {
                    $text = $userInfo->name . ' has commented on your Feed.';
                    $payload = [
                        'mtitle'            => '',
                        'mdesc'             => $text,
                        'feed_id'           => $request->get('feed_id'),
                        'to_user_id'        => $feedInfo->user->id,
                        'feed_type'         => $feedInfo->feed_type,
                        'from_user_id'      => $userInfo->id,
                        'mtype'             => 'NEW_COMMENT'
                    ];

                    $storeNotification = [
                        'user_id'           => $feedInfo->user->id,
                        'from_user_id'      => $userInfo->id,
                        'description'       => $text,
                        'feed_id'           => $request->get('feed_id'),
                        'notification_type' => 'NEW_COMMENT'
                    ];

                    access()->addNotification($storeNotification);
                    access()->sentPushNotification($feedInfo->user, $payload);
                }

                if(isset($feedInfo->feed_tag_users) && count($feedInfo->feed_tag_users))
                {
                    $text = $userInfo->name . ' commented on a post you are tagged in.';
                    foreach($feedInfo->feed_tag_users as $tagUser)
                    {
                        if($userInfo->id == $tagUser->user->id)
                        {
                            continue;
                        }

                        $payload = [
                            'mtitle'            => '',
                            'mdesc'             => $text,
                            'feed_id'           => $request->get('feed_id'),
                            'to_user_id'        => $tagUser->user->id,
                            'feed_type'         => $feedInfo->feed_type,
                            'from_user_id'      => $userInfo->id,
                            'mtype'             => 'NEW_COMMENT_TAG_USERS'
                        ];

                        $storeNotification = [
                            'user_id'           => $tagUser->user->id,
                            'from_user_id'      => $userInfo->id,
                            'description'       => $text,
                            'feed_id'           => $request->get('feed_id'),
                            'notification_type' => 'NEW_COMMENT_TAG_USERS'
                        ];

                        access()->addNotification($storeNotification);
                        access()->sentPushNotification($tagUser->user, $payload);
                    }
                }
                /*$text = $userInfo->name . ' has commented on your Feed.';
                $payload = [
                    'mtitle'            => '',
                    'mdesc'             => $text,
                    'feed_id'           => $request->get('feed_id'),
                    'to_user_id'        => $feedOwner->user->id,
                    'from_user_id'      => $userInfo->id,
                    'mtype'             => 'NEW_COMMENT'
                ];

                $storeNotification = [
                    'user_id'           => $feedOwner->user->id,
                    'from_user_id'      => $userInfo->id,
                    'description'       => $text,
                    'feed_id'           => $model->id,
                    'notification_type' => 'NEW_COMMENT'
                ];

                access()->addNotification($storeNotification);
                access()->sentPushNotification($feedOwner->user, $payload);*/

                $response = [
                    'comment_id' => (int) $model->id,
                    'feed_id'    => (int) $model->feed_id,
                    'user_id'    => (int) $userInfo->id,
                    'comment'    => $request->get('comment'),
                    'profile_pic'   =>  URL::to('/').'/uploads/user/' . $userInfo->profile_pic,
                    'create_at'  => date('m/d/Y h:i:s', strtotime($model->created_at))
                ];

                return $this->successResponse($response, 'Comments is Created Successfully');
            }
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function deletecomment(Request $request)
    {
        if($request->has('comment_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $model      = $this->repository->model->where([
                'user_id'   => $userInfo->id,
                'id'        => $request->get('comment_id'),
            ])->first();

            if(isset($model) && isset($model->id))
            {
                if($model->delete())
                {
                    $message = [
                        'message' => 'Comment Deleted successfully'
                    ];
                    return $this->successResponse($message, 'Comments is Created Successfully');
                }
            }
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'No Comment Found or Invalid Input'
            ], 'Something went wrong !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function reportcomment(Request $request)
    {
        if($request->has('comment_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $model      = $this->repository->model->where([
                'id' => $request->get('comment_id'),
            ])->first();

            if(isset($model) && isset($model->id))
            {
                if($model->delete())
                {
                    $reportData = [
                        'user_id'       => $model->user_id,
                        'feed_id'       => $model->feed_id,
                        'reporter_id'   => $userInfo->id,
                        'comment'       => $model->comment,
                        'comment_id'    => $request->get('comment_id')
                    ];
                    
                    ReportComments::create($reportData);
                    
                    $message = [
                        'message' => 'Comment Reported successfully'
                    ];
                    return $this->successResponse($message, 'Reported Successfully');
                }
            }
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'No Comment Found or Invalid Input'
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
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $itemData = $this->repository->getById($itemId);

            if($itemData)
            {
                $responseData = $this->commentsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'View Item');
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
        if($request->has('comment_id') && $request->has('feed_id'))
        {
            $model = $this->repository->model->where([
                'id'        => $request->get('comment_id'),
                'feed_id'   => $request->get('feed_id')
            ])->first();
                
            if(isset($model) && isset($model->id))
            {
                $model->comment = $request->get('comment');

                if($model->save())
                {
                    $response = [
                        'message' => 'comment updated successfully'
                    ];
                    return $this->successResponse($response, 'comment updated successfully');
                }
            }

            return $this->setStatusCode(400)->failureResponse([
                'reason' => 'No Comment Found!'
            ], 'No Comment Found!');
        }

        return $this->setStatusCode(400)->failureResponse([
                'reason' => 'Invalid Inputs'
            ], 'Invalid Inputs!');
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
                    'success' => 'Comments Deleted'
                ], 'Comments is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}