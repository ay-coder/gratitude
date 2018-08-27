<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\PostsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Posts\EloquentPostsRepository;
use Illuminate\Support\Facades\Validator;
use App\Models\Access\User\User;
use App\Models\ReadPost\ReadPost;
use App\Library\Push\PushNotification;
use App\Models\Notifications\Notifications;
use App\Models\Connections\Connections;
use Auth;

class APIPostsController extends BaseApiController
{
    /**
     * Posts Transformer
     *
     * @var Object
     */
    protected $postsTransformer;

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
    protected $primaryKey = 'postsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentPostsRepository();
        $this->postsTransformer = new PostsTransformer();
    }

    /**
     * List of All Posts
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $paginate   = $request->get('paginate') ? $request->get('paginate') : false;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $condition  = ['tag_user_id' => $userInfo->id];


        $connectionModel        = new Connections;
        $myConnectionList       = $connectionModel->where('is_accepted', 1)->where('user_id', $userInfo->id)
        ->pluck('other_user_id')
        ->toArray();

        $otherConnectionList    = $connectionModel->where('is_accepted', 1)
        ->where('other_user_id', $userInfo->id)
        ->pluck('requested_user_id')
        ->toArray();
            
        $allConnections = array_merge($myConnectionList, $otherConnectionList);
        $connectionIds  = array_unique($allConnections);
        
        $items      = $this->repository->model->with([
            'user', 'tag_user', 'views', 'comments'
        ])
        ->where('user_id', '!=', $userInfo->id)
        ->where('is_accepted', 1)
        ->whereIn('tag_user_id', $connectionIds)
        ->orWhereIn('user_id', $connectionIds)
        ->orderBy($orderBy, $order)
        ->get();
        
        if(isset($items) && count($items))
        {
            $itemsOutput = $this->postsTransformer->getUserPosts($userInfo, $items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Posts!'
            ], 'No Posts Found !');
    }

    /**
     * List of All Posts
     *
     * @param Request $request
     * @return json
     */
    public function postFilter(Request $request)
    {
        $search     = $request->has('keyword') ? $request->get('keyword') : '';
        $userInfo   = $this->getAuthenticatedUser();
        $paginate   = $request->get('paginate') ? $request->get('paginate') : false;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        //$condition  = ['tag_user_id' => $userInfo->id];
        $condition  = [];
        $items      = $this->repository->model->with([
            'user', 'tag_user', 'views', 'comments'
        ])
        ->where('description', 'LIKE', '%' .$search. '%')
        ->orderBy($orderBy, $order)
        ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->postsTransformer->getUserPosts($userInfo, $items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Posts!'
            ], 'No Posts Found !');
    }

    /**
     * List of All Posts
     *
     * @param Request $request
     * @return json
     */
    public function my(Request $request)
    {
        if($request->get('user_id'))
        {
            $userInfo = User::where('id', $request->get('user_id'))->first();
        }
        else
        {
            $userInfo   = $this->getAuthenticatedUser();
        }
        $paginate   = $request->get('paginate') ? $request->get('paginate') : false;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $condition  = ['tag_user_id' => $userInfo->id];
        $items      = $paginate ? $this->repository->model->with(['user', 'tag_user', 'views', 'comments'])->where($condition)->orderBy($orderBy, $order)->paginate($paginate)->items() : $this->repository->getAll($condition, $orderBy, $order);

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->postsTransformer->getMyPosts($userInfo, $items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Posts!'
            ], 'No Posts Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_user_id'   => 'required',
            'media'         => 'required'
        ]);

        if($validator->fails()) 
        {
            $messageData = '';

            foreach($validator->messages()->toArray() as $message)
            {
                $messageData = $message[0];
            }
            return $this->failureResponse($validator->messages(), $messageData);
        }

        $input      = $request->all();
        $userInfo   = $this->getAuthenticatedUser();
        $tagUser    = User::where('id', $request->get('tag_user_id'))->first();
        $input      = array_merge($input, [
            'is_image'  => 1, 
            'is_video'  => 0, 
            'user_id'   => $userInfo->id,
            'media'     => 'default.png',
            'thumbnail' => 'default.png'
        ]);

        if($request->file('media'))
        {
            $imageName  = rand(11111, 99999) . '_media.' . $request->file('media')->getClientOriginalExtension();
            $request->file('media')->move(base_path() . '/public/uploads/media/', $imageName);
            $input = array_merge($input, ['media' => $imageName]);

            if($request->get('is_video') && $request->get('is_video') == 1)
            {
                $input = array_merge($input, ['is_image' => 0, 'is_video' => 1]);                
            }
        }

        if($request->file('thumbnail'))
        {
            $imageName  = rand(11111, 99999) . '_thumbnail.' . $request->file('thumbnail')->getClientOriginalExtension();
            $request->file('thumbnail')->move(base_path() . '/public/uploads/media/', $imageName);
            $input = array_merge($input, ['thumbnail' => $imageName]);
        }

        $model = $this->repository->create($input);

        if($model)
        {
            $text       = $userInfo->name . ' has tagged you in a post';
            $payload    = [
                'mtitle'            => '',
                'mdesc'             => $text,
                'post_id'           => $model->id,
                'user_id'           => $userInfo->id,
                'tagged_user_id'    => $tagUser->id,
                'mtype'             => 'NEW_POST'
            ];
            
            Notifications::create([
                'user_id'           => $tagUser->id,
                'to_user_id'        => $userInfo->id,
                'description'       => $text,
                'post_id'           => $model->id,
                'notification_type' => 'NEW_POST'
            ]);

            if(isset($tagUser->device_token))
            {
                PushNotification::iOS($payload, $tagUser->device_token);
            }

            $responseData = $this->postsTransformer->transform($model);

            return $this->successResponse($responseData, 'Post Created Successfully');
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
        $itemId = (int) hasher()->decode($request->get('post_id'));

        if($itemId)
        {
            $postInfo = $this->repository->model->with(['user', 'tag_user', 'comments', 'views'])->where('id', $itemId)->first();
            
            if($postInfo)
            {
                $userId     = Auth::user()->id;
                $readPost   = new ReadPost;

                $readCount  = $readPost->where([
                    'user_id'   => $userId,
                    'post_id'   => $itemId
                ])->count();

                if($readCount == 0)
                {
                    $readPost->create([
                        'user_id'   => $userId,
                        'post_id'   => $itemId
                    ]);
                }

                $responseData = $this->postsTransformer->singlePost($postInfo);

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
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->update($itemId, $request->all());

            if($status)
            {
                $itemData       = $this->repository->getById($itemId);
                $responseData   = $this->postsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Posts is Edited Successfully');
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
                    'success' => 'Posts Deleted'
                ], 'Posts is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }


    /**
     * List of All Posts Requests
     *
     * @param Request $request
     * @return json
     */
    public function list(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $condition  = ['tag_user_id' => $userInfo->id];
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $items      = $this->repository->model->with([
            'user', 'tag_user', 'views', 'comments'
        ])
        ->where($condition)
        ->where('is_accepted', 0)
        ->orderBy($orderBy, $order)
        ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->postsTransformer->getMyPosts($userInfo, $items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Post Request!'
            ], 'No Posts Request Found !');
    } 

    /**
     * Accept Post Requests
     *
     * @param Request $request
     * @return json
     */
    public function accept(Request $request)
    {
        if($request->has('post_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $model      = $this->repository->model->where([
                'id'          => $request->get('post_id'),
                'tag_user_id' => $userInfo->id,
                'is_accepted' => 0
            ])->first();

            if(isset($model->id))
            {
                $postOwner = User::where('id', $model->user_id)->first();
                $model->is_accepted = 1;
                if($model->save())
                {
                    $text       = $userInfo->name . ' has approved your post';
                    $payload    = [
                        'mtitle'            => '',
                        'mdesc'             => $text,
                        'post_id'           => $model->id,
                        'user_id'           => $postOwner->id,
                        'tagged_user_id'    => $model->tag_user_id,
                        'mtype'             => 'APPROVED_POST'
                    ];
                    
                    Notifications::create([
                        'user_id'           => $model->tag_user_id,
                        'to_user_id'        => $postOwner->id,
                        'description'       => $text,
                        'post_id'           => $model->id,
                        'user_id'           => $postOwner->id,
                        'tagged_user_id'    => $model->tag_user_id,
                        'notification_type' => 'APPROVED_POST'
                    ]);

                    if(isset($postOwner->device_token))
                    {
                        PushNotification::iOS($payload, $postOwner->device_token);
                    }

                    Notifications::where([
                        'post_id'           => $request->get('post_id'),
                        'notification_type' => 'NEW_POST'
                    ])->delete();

                    $message = [
                        'message' => 'Post Request accepted !'
                    ];

                    return $this->successResponse($message);
                }
            }
        }
           
        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Post Request!'
            ], 'No Posts Request Found !');
    } 

    /**
     * Accept Post Requests
     *
     * @param Request $request
     * @return json
     */
    public function reject(Request $request)
    {
        if($request->has('post_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $model      = $this->repository->model->where([
                'id'          => $request->get('post_id'),
                'tag_user_id' => $userInfo->id,
                'is_accepted' => 0
            ])->first();

            if(isset($model->id))
            {
                if($model->delete())
                {
                    $message = [
                        'message' => 'Post Request Deleted !'
                    ];

                    return $this->successResponse($message);
                }
            }
        }
           
        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Post Request!'
            ], 'No Posts Request Found !');
    }   
}