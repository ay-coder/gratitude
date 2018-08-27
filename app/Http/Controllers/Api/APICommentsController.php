<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\CommentsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Comments\EloquentCommentsRepository;
use Illuminate\Support\Facades\Validator;
use App\Models\Blocked\Blocked;
use App\Library\Push\PushNotification;
use App\Models\Notifications\Notifications;
use App\Models\Posts\Posts;
use App\Models\Access\User\User;

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
    protected $primaryKey = 'comment_id';

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
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id'   => 'required',
            'comment'   => 'required'
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

        $userInfo   = $this->getAuthenticatedUser();
        $postInfo   = Posts::where('id', $request->get('post_id'))->first();
        $tagUser    = User::where('id', $postInfo->tag_user_id)->first();
        $input      = array_merge($request->all(), ['user_id' => $userInfo->id]);
        $model      = $this->repository->create($input);

        if($model)
        {
            $text       = $userInfo->name . '  has commented on your post';
            $payload    = [
                'mtitle'    => '',
                'mdesc'     => $text,
                'post_id'   => $model->post_id,
                'comment_id' => $model->id,
                'mtype'      => 'NEW_COMMENT'
            ];
            
            Notifications::create([
                'user_id'           => $tagUser->id,
                'to_user_id'        => $userInfo->id,
                'description'       => $text,
                'post_id'           => $model->post_id,
                'comment_id'        => $model->id,
                'notification_type' => 'NEW_POST'
            ]);

            if(isset($tagUser->device_token))
            {
                PushNotification::iOS($payload, $tagUser->device_token);
            }

            return $this->successResponse(['message' => 'Comment Created Successfully!'], 'Comments is Created Successfully');
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
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->update($itemId, $request->all());

            if($status)
            {
                $itemData       = $this->repository->getById($itemId);
                $responseData   = $this->commentsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Comments is Edited Successfully');
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
        $validator = Validator::make($request->all(), [
            'comment_id'   => 'required'
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

        if($request->has('comment_id'))
        {
            $itemId     = $request->get('comment_id');
            $comment    = $this->repository->model->find($itemId);
            $userInfo   = $this->getAuthenticatedUser();

            if($comment->user_id == $userInfo->id || $userInfo->posts()->where('post_id', $comment->post_id))
            {
                $status = $this->repository->destroy($itemId);

                if($status)
                {
                    return $this->successResponse([
                        'success' => 'Comments Deleted'
                    ], 'Comments is Deleted Successfully');
                }
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Blocked
     *
     * @param Request $request
     * @return string
     */
    public function blocked(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id'   => 'required'
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

        if($request->has('comment_id'))
        {
            $itemId     = $request->get('comment_id');
            $comment    = $this->repository->model->find($itemId);
            $userInfo   = $this->getAuthenticatedUser();

            if(isset($comment->id))
            {
                Blocked::create([
                    'blocked_by'    => $userInfo->id,
                    'user_id'       => $comment->user_id,
                    'post_id'       => $comment->post_id,
                    'comment'       => $comment->comment
                ]);

                $status = $this->repository->destroy($itemId);

                if($status)
                {
                    return $this->successResponse([
                        'success' => 'Comments Blocked'
                    ], 'Comments is Blocked Successfully');
                }
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}