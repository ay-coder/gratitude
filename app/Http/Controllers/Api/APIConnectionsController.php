<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\ConnectionsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Connections\EloquentConnectionsRepository;
use App\Models\Access\User\User;
use App\Models\Connections\Connections;
use Illuminate\Support\Facades\Validator;
use App\Library\Push\PushNotification;
use App\Models\FeedNotifications\FeedNotifications;

class APIConnectionsController extends BaseApiController
{
    /**
     * Connections Transformer
     *
     * @var Object
     */
    protected $connectionsTransformer;

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
    protected $primaryKey = 'connectionsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository             = new EloquentConnectionsRepository();
        $this->connectionsTransformer = new ConnectionsTransformer();
        $this->connectionModel        = new Connections;
    }

    /**
     * List of All Connections
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $loginUser              = $this->getAuthenticatedUser();
        $userModel              = new User;   
        $userId                 = $request->has('user_id') ? $request->get('user_id') : $loginUser->id;
        $userInfo               = User::where('id', $userId)->first();
        $blockUserIds           = access()->getBlockUserIds($userInfo->id);
        if(! $userInfo)
        {
            return $this->setStatusCode(400)->failureResponse([
            'message' => 'Invalid User Id!'
            ], 'Invalid Input !');
        }
        $connectionModel        = new Connections;
        $myConnectionList       = $connectionModel->where('is_accepted', 1)->where('user_id', $userInfo->id)->pluck('other_user_id')->toArray();
        $otherConnectionList    = $connectionModel->where('is_accepted', 1)->where('other_user_id', $userInfo->id)->pluck('requested_user_id')->toArray();
        
        $orConnections = [];

        foreach($otherConnectionList as $other)
        {
            if(in_array($other,  $blockUserIds))
            {
                continue;
            }

            $orConnections[] = $other;
        }

        $items = $userModel->whereNotIn('id', $blockUserIds )
                    ->where('id', '!=', $userInfo->id)
                    ->whereIn('id', $myConnectionList)
                    ->orWhereIn('id', $orConnections)
                    ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->connectionsTransformer->connectionTransform($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Connections!'
            ], 'No Connections Found !');
    }

    /**
     * My Connections
     * 
     * @param Request $request
     * @return json
     */
    public function myConnections(Request $request)
    {
        $userInfo               = $request->get('user_id') ? User::find($request->get('user_id')) : $this->getAuthenticatedUser();
        $userModel              = new User;   
        $connectionModel        = new Connections;
        $myConnectionList       = $connectionModel->where('is_accepted', 1)->where('user_id', $userInfo->id)->pluck('other_user_id')->toArray();
         $otherConnectionList    = $connectionModel->where('is_accepted', 1)->where('other_user_id', $userInfo->id)->pluck('requested_user_id')->toArray();

        $blockUserIds = access()->getBlockUserIds($userInfo->id);
            
        $items = $userModel->where('id', '!=', $userInfo->id)
                    ->whereNotIn('id', $blockUserIds)
                    ->whereIn('id', $myConnectionList)
                    ->orWhereIn('id', $otherConnectionList)
                    ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->connectionsTransformer->connectionTransform($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Connections!'
            ], 'No Connections Found !');    
    }

    /**
     * List of All Connections
     *
     * @param Request $request
     * @return json
     */
    public function search(Request $request)
    {
        $userInfo               = $this->getAuthenticatedUser();
        $keyword                = $request->get('keyword');
        $connectionModel        = new Connections;
        $myConnectionList       = $connectionModel->where([
            'user_id'       => $userInfo->id,
            'is_accepted'   => 1
        ])->pluck('other_user_id')->toArray();

        $otherConnectionList    = $connectionModel->where([
            'other_user_id' => $userInfo->id,
            'is_accepted'   => 1
        ])->pluck('requested_user_id')->toArray();

        $blockUserIds = access()->getBlockUserIds($userInfo->id);

        $userModel              = new User;   
        $allConnections         = array_merge($myConnectionList, $otherConnectionList);
        $allConnections         = array_unique($allConnections);

        $userRequestIds         = $connectionModel->where([
            'user_id'       => $userInfo->id,
            'is_accepted'   => 0
        ])->pluck('other_user_id')->toArray();

        $myRequestIds         = $connectionModel->where([
            'other_user_id'       => $userInfo->id,
            'is_accepted'   => 0
        ])->pluck('other_user_id')->toArray();


        $myRequestIds = array_merge($userRequestIds, $myRequestIds);
        $myRequestIds = array_unique($myRequestIds);        

        if(1==1)
        {
            $suggestions = $userModel->whereNotIn('id', $myRequestIds)
                      ->where('id', '!=', $userInfo->id)
                      ->whereNotIn('id', $blockUserIds)
                      ->where(function($q) use($keyword)
                      {
                        $q->where('name', 'LIKE', '%'. $keyword .'%')
                        ->orwhere('email', 'LIKE', '%'. $keyword .'%');
                      })
                      ->get();
            if(isset($suggestions) && count($suggestions))
            {
                $itemsOutput = $this->connectionsTransformer->searchUserTranform($suggestions, $allConnections, $userRequestIds, $userInfo, $myRequestIds);

                if(count($itemsOutput) && isset($itemsOutput))
                {
                    return $this->successResponse($itemsOutput);
                }

                return $this->successResponse([], 'No Result Found !');
            }
        }
        

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Connections!'
            ], 'No Connections Found !');
    }

    public function searchAppUsers(Request $request)
    {
        $userInfo               = $this->getAuthenticatedUser();
        $keyword                = $request->get('keyword');
        $connectionModel        = new Connections;
        $myConnectionList       = $connectionModel->where([
            'user_id'       => $userInfo->id,
            
        ])->pluck('other_user_id')->toArray();
        $otherConnectionList    = $connectionModel->where([
            'other_user_id' => $userInfo->id,
            
        ])->pluck('requested_user_id')->toArray();
        $userModel              = new User;   
        $allConnections         = array_merge($myConnectionList, $otherConnectionList);
        $allConnections         = array_unique($allConnections);

        $blockUserIds = access()->getBlockUserIds($userInfo->id);

        $userRequestIds         = $connectionModel->where([
            'user_id'       => $userInfo->id,
            'is_accepted'   => 0
        ])->pluck('other_user_id')->toArray();

        $myRequestIds         = $connectionModel->where([
            'other_user_id'       => $userInfo->id,
            'is_accepted'   => 0
        ])->pluck('other_user_id')->toArray();

        if(1==1)
        {
            $suggestions = $userModel->whereNotIn('id', $otherConnectionList)
                      ->whereNotIn('id', $myConnectionList)
                      ->whereNotIn('id', $$blockUserIds)
                      ->whereNotIn('id', $userRequestIds)
                      ->whereNotIn('id', $myRequestIds)
                      ->whereNotIn('id', $allConnections)
                      ->where('id', '!=', $userInfo->id)
                      ->where('name', 'LIKE', '%'. $keyword .'%')
                      ->orwhere('email', 'LIKE', '%'. $keyword .'%')
                      ->get();
            if(isset($suggestions) && count($suggestions))
            {
                $itemsOutput = $this->connectionsTransformer->searchAppUserTranform($suggestions, $userInfo, $allConnections);

                if(count($itemsOutput) && isset($itemsOutput))
                {
                    return $this->successResponse($itemsOutput);
                }

                return $this->successResponse([], 'No Result Found !');
            }
        }
        

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Connections!'
            ], 'No Connections Found !');
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
            'user_id'   => 'required'
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
        if($request->get('user_id') == $userInfo->id)
        {
            return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Input'
            ], 'Invalid Input!');
        }

        $inConnection = $this->connectionModel->where([
            'other_user_id' => $userInfo->id,
            'user_id'       => $request->get('user_id')
            ])->first();


        if($inConnection)
        {
            if($inConnection->requested_user_id == $userInfo->id && $inConnection->is_accepted == 0)
            {
                return $this->setStatusCode(400)->failureResponse([
                'reason' => 'Already Requested!'
                ], 'Already Requested!');
            }

            return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Already In Connection'
            ], 'Already In Connection!');
        }   

        $outConnection = $this->connectionModel->where([
            'other_user_id' => $request->get('user_id'),
            'user_id'       => $userInfo->id
            ])->first();

        if($outConnection)
        {
            if($outConnection->requested_user_id == $userInfo->id && $inConnection->is_accepted == 0)
            {
                return $this->setStatusCode(400)->failureResponse([
                'reason' => 'Already Requested!'
                ], 'Already Requested!');
            }

            return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Already In Connection'
            ], 'Already In Connection !');
        }

        $blockedUserIds = access()->getBlockUserIds($userInfo->id);

        if(in_array($request->get('user_id'), $blockedUserIds))
        {
            return $this->setStatusCode(400)->failureResponse([
            'reason' => 'User Blocked!'
            ], 'User Blocked!');
        }

        $input      = [
            'user_id'               => $userInfo->id,
            'requested_user_id'     => $userInfo->id,
            'other_user_id'         => $request->get('user_id'),
            'is_accepted'           => 0
        ];
            
        $model = $this->repository->create($input);
        $requestedUser = User::where('id', $request->get('user_id'))->first();

        $otherBlockedUserIds = access()->getBlockUserIds($request->get('user_id'));

        if(!in_array($userInfo->id, $otherBlockedUserIds))
        {
            $text       = $userInfo->name . ' has sent you a friend request';
            $payload    = [
                'mtitle'            => '',
                'mdesc'             => $text,
                'user_id'           => $userInfo->id,
                'other_user_id'     => $requestedUser->id,
                'badgeCount'        => access()->getUnreadNotificationCount($requestedUser->id),
                'mtype'             => 'NEW_CONNECTION'
            ];
            
            FeedNotifications::create([
                'user_id'           => $requestedUser->id,
                'from_user_id'      => $userInfo->id,
                'description'       => $text,
                'icon'              => 'NEW_CONNECTION.png',
                'notification_type' => 'NEW_CONNECTION'
            ]);

            access()->sentPushNotification($requestedUser, $payload); 
        }

        if($model)
        {
            return $this->successResponse(['message' => 'Request Added Successfully !'], 'Connection Request sent');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    
    public function showRequests(Request $request)
    {
        $userInfo           = $this->getAuthenticatedUser();
        $connectionModel    = new Connections;
        $blockUserIds       = access()->getBlockUserIds($userInfo->id);

        $allRequests = $connectionModel->with('user')->whereNotIn('user_id', $blockUserIds)->where(['other_user_id' => $userInfo->id,
            'is_accepted' => 0
        ])->get();

        if($allRequests)
        {
            $itemsOutput = $this->connectionsTransformer->requestTransform($allRequests);

            return $this->successResponse($itemsOutput);
            
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'No Requests Found'
            ], 'No Pending Request Found!');

    }

    /**
     * Show My Requests
     * 
     * @param Request $request
     * @return array
     */
    public function showMyRequests(Request $request)
    {
        $userInfo           = $this->getAuthenticatedUser();
        $connectionModel    = new Connections;
        $blockUserIds       = access()->getBlockUserIds($userInfo->id);

        $allRequests = $connectionModel->with('user')->whereNotIn('user_id', $blockUserIds)->where([
            'requested_user_id' => $userInfo->id,
            'is_accepted'       => 0
        ])->get();

        if($allRequests)
        {
            $itemsOutput = $this->connectionsTransformer->myRequestTransform($allRequests);

            return $this->successResponse($itemsOutput);
            
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'No Requests Found'
            ], 'No Pending Request Found!');

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
                $responseData = $this->connectionsTransformer->transform($itemData);

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
                $responseData   = $this->connectionsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Connections is Edited Successfully');
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
        if($request->has('user_id'))
        {
            $userInfo               = $this->getAuthenticatedUser();
            $connectionModel        = new Connections;

            $connection = $connectionModel->where([
                    'user_id'       => $userInfo->id,
                    'other_user_id' => $request->get('user_id')
            ])->first();

            if(isset($connection))
            {
                $connection->delete();

                return $this->successResponse([
                    'success' => 'Connections Deleted'
                ], 'Connections is Deleted Successfully');
            }
            

            $connection = $connectionModel->where([
                    'other_user_id' => $userInfo->id,
                    'user_id'       => $request->get('user_id')
            ])->first();

            if(isset($connection))
            {
                $connection->delete();

                return $this->successResponse([
                    'success' => 'Connections Deleted'
                ], 'Connections is Deleted Successfully');
            }
            
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Block
     *
     * @param Request $request
     * @return string
     */
    public function block(Request $request)
    {
        if($request->has('user_id'))
        {
            $userInfo               = $this->getAuthenticatedUser();
            $connectionModel        = new Connections;

            $connection = $connectionModel->where([
                    'user_id'       => $userInfo->id,
                    'other_user_id' => $request->get('user_id')
            ])->first();

            if(isset($connection))
            {
                $connection->delete();

                return $this->successResponse([
                    'success' => 'Connections Blocked'
                ], 'Connections is Blocked Successfully');
            }
            

            $connection = $connectionModel->where([
                    'other_user_id' => $userInfo->id,
                    'user_id'       => $request->get('user_id')
            ])->first();

            if(isset($connection))
            {
                $connection->delete();

                return $this->successResponse([
                    'success' => 'Connections Blocked'
                ], 'Connections is Blocked Successfully');
            }
            
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    public function acceptRequests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required'
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


        $connectionModel = new Connections;

        $userInfo   = $this->getAuthenticatedUser();
        $connection = $connectionModel->where([
            'user_id'       => $request->get('user_id'),
            'other_user_id' => $userInfo->id
        ])
        ->orWhere([
            'user_id'       => $userInfo->id,
            'other_user_id' => $request->get('user_id'),
        ])->first();

        if(isset($connection) && $connection->other_user_id == $userInfo->id && $connection->is_accepted == 0)
        {
            $connection->is_accepted = 1;   
            $connection->save();

            $text           = $userInfo->name . ' has accepted your friend request';
            $requestedUser  = User::where('id', $request->get('user_id'))->first();
            $payload    = [
                'mtitle'            => '',
                'mdesc'             => $text,
                'user_id'           => $requestedUser->id,
                'tagged_user_id'    => $userInfo->id,
                'badgeCount'        => access()->getUnreadNotificationCount($requestedUser->id),
                'mtype'             => 'ACCEPT_CONNECTION'
            ];
            
            FeedNotifications::create([
                'user_id'           => $requestedUser->id,
                'from_user_id'      => $userInfo->id,
                'description'       => $text,
                'icon'              => 'ACCEPT_CONNECTION.png',
                'notification_type' => 'CONNECTION_ACCEPTED'
            ]);

            access()->sentPushNotification($requestedUser, $payload);

            return $this->successResponse(['message' => 'Request Accepted Successfully !'], 'Connection added Successfully');
        }
       
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    public function rejectRequests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required'
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

        $connectionModel = new Connections;
        $userInfo   = $this->getAuthenticatedUser();
        $connection = $connectionModel->where([
            'user_id'       => $request->get('user_id'),
            'other_user_id' => $userInfo->id
        ])
        ->orWhere([
            'user_id'       => $userInfo->id,
            'other_user_id' => $request->get('user_id')
        ])->first();
        

        if(isset($connection) && isset($connection->id))
        {
            $connection->delete();   

            return $this->successResponse(['message' => 'Request Declined Successfully !'], 'Connection Removed Successfully');
        }
       
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Remove My Request
     * 
     * @param  Request $request
     * @return array
     */
    public function removeMyRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required'
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

        $connectionModel = new Connections;
        $userInfo   = $this->getAuthenticatedUser();
        $connection = $connectionModel->where([
            'id'                => $request->get('request_id'),
            'requested_user_id' => $userInfo->id
        ])->first();
        

        if(isset($connection) && isset($connection->id))
        {
            $connection->delete();   

            return $this->successResponse(['message' => 'Request Removed Successfully !'], 'Connection Request Removed Successfully');
        }
       
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Search Global
     * 
     * @param Request $request
     */
    public function searchGlobal(Request $request)   
    {
        $userInfo               = $this->getAuthenticatedUser();
        $connectionModel        = new Connections;
        $myConnectionList       = $connectionModel->where('user_id', $userInfo->id)->pluck('other_user_id')->toArray();
        $otherConnectionList    = $connectionModel->where('other_user_id', $userInfo->id)->pluck('requested_user_id')->toArray();

        $blockUserIds           = access()->getBlockUserIds($userInfo->id);
        $userModel              = new User;   

        $suggestions = $userModel->whereNotIn('id', $otherConnectionList)
                      ->whereNotIn('id', $myConnectionList)
                      ->whereNotIn('id', $blockUserIds)
                      ->where('id', '!=', $userInfo->id)
                      ->get();
        
        if(isset($suggestions) && count($suggestions))
        {
            $itemsOutput = $this->connectionsTransformer->searchTranform($suggestions);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Suggestion!'
            ], 'No Suggestions Found !');       
    }
}