<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Access\User\User;
use Response;
use Carbon;
use App\Repositories\Backend\User\UserContract;
use App\Repositories\Backend\UserNotification\UserNotificationRepositoryContract;
use App\Http\Transformers\UserTransformer;
use App\Http\Utilities\FileUploads;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use App\Http\Controllers\Api\BaseApiController;
use Auth;
use App\Repositories\Backend\Access\User\UserRepository;
use Illuminate\Support\Facades\Validator;
use App\Models\Connections\Connections;
use App\Library\Push\PushNotification;
use App\Models\Categories\Categories;
use URL;

class UsersController extends BaseApiController
{
    protected $userTransformer;
    /**
     * __construct
     */
    public function __construct()
    {
        $this->userTransformer  = new UserTransformer;
        
    }

    /**
     * Login request
     * 
     * @param Request $request
     * @return type
     */
    public function login(Request $request) 
    {
        $credentials = $request->only('username', 'password');

        try {
            // verify the credentials and create_function(args, code) a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error'     => 'Invalid Credentials',
                    'message'   => 'No User Found for given details',
                    'status'    => false,
                    ], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json([
                    'error'     => 'Somethin Went Wrong!',
                    'message'   => 'Unable to Generate Token!',
                    'status'    => false,
                    ], 500);
        }
        

        if($request->get('device_token'))
        {
            $user = Auth::user();
            $user->device_token = $request->get('device_token');
            $user->save();
        }

        $user = Auth::user()->toArray();
        $userData = array_merge($user, ['token' => $token]);

        $responseData = $this->userTransformer->transform((object)$userData);

        return $this->successResponse($responseData);
    }

    /**
     * Logout request
     * @param  Request $request
     * @return json
     */
    public function logout(Request $request) 
    {
        $userInfo   = $this->getApiUserInfo();
        $user       = User::find($userInfo['userId']);

        $user->device_token = '';

        if($user->save()) 
        {
            $successResponse = [
                'message' => 'User Logged out successfully.'
            ];

            return $this->successResponse($successResponse);
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'User Not Found !'
        ], 'User Not Found !');
    }

    /**
     * socialCreate
     *
     * @param Request $request
     * @return string
     */
    public function socialCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'social_token'  => 'required|unique:users|max:255'
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

        $user = User::where([
            'social_token' => $request->get('social_token')
        ])->first();

        if(isset($user) && $user->id)
        {
            return $this->socialLogin($request);
        }

        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'social_token'  => 'required|unique:users|max:255'
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

        $status = $this->socialLogin($request);

        $repository = new UserRepository;
        $input      = $request->all();
        $input      = array_merge($input, ['profile_pic' => 'default.png']);
        

        $user = $repository->createSocialUserStub($input);
        if($user)
        {
            Auth::loginUsingId($user->id, true);

            $user           = Auth::user()->toArray();
            $token          = JWTAuth::fromUser(Auth::user());
            $userData       = array_merge($user, ['token' => $token]);
            $responseData   = $this->userTransformer->transform((object)$userData);
            return $this->successResponse($responseData);
        }
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * Login request
     *
     * @param Request $request
     * @return type
     */
    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'social_token'      => 'required'
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


        $user = User::where([
            'social_token'=> $request->get('social_token')
        ])->first();

        if(isset($user) && $user->id)
        {
            Auth::loginUsingId($user->id, true);

            if($request->get('device_token') && $request->get('device_type'))
            {
                $user = Auth::user();
                $user->device_type  = $request->get('device_type');
                $user->device_token = $request->get('device_token');
                $user->save();
            }

            $user       = Auth::user()->toArray();
            $token      = JWTAuth::fromUser(Auth::user());
            $userData   = array_merge($user, ['token' => $token]);
            $responseData = $this->userTransformer->transform((object)$userData);

            return $this->successResponse($responseData);
        }

        return response()->json([
            'error'     => 'Invalid Credentials',
            'message'   => 'No User Found for given details',
            'status'    => false,
            ], 401);
    }


    /**
     * Config
     * 
     * @param  Request $request [description]
     * @return json
     */
    public function config(Request $request)
    {
        $categories     =  Categories::getAll();
        $categoryData   = [];

        if(isset($categories) && count($categories))
        {
            foreach($categories as $category)
            {
                $categoryData[] = [
                    'category_id'   => (int) $category->id,
                    'title'         => $category->title,
                    'icon'          => URL::to('/').'/uploads/categories/' . $category->icon
                ];
            }
            
        }
        $successResponse = [
            'support_number'        => '110001010',
            'rateus_url'            => route('frontend.privacy-policy'),
            'privacy_policy_url'    => route('frontend.privacy-policy'),
            'about_us_url'          => route('frontend.about-us'),
            'terms_conditions_url'  => route('frontend.terms-conditions'),
        ];

        return $this->successResponse($successResponse);
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $repository = new UserRepository;
        $input      = $request->all();
        $input      = array_merge($input, ['profile_pic' => 'default.png']);

        if($request->file('profile_pic'))
        {
            $imageName  = rand(11111, 99999) . '_user.' . $request->file('profile_pic')->getClientOriginalExtension();
            if(strlen($request->file('profile_pic')->getClientOriginalExtension()) > 0)
            {
                $request->file('profile_pic')->move(base_path() . '/public/uploads/user/', $imageName);
                $input = array_merge($input, ['profile_pic' => $imageName]);
            }
        }

        $validator = Validator::make($request->all(), [
            'username'  => 'required|unique:users|max:255',
            'name'      => 'required',
            'password'  => 'required',
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

        $user = $repository->createUserStub($input);

        if($user)
        {
            Auth::loginUsingId($user->id, true);

            $credentials = [
                'username'  => $input['username'],
                'password'  => $input['password']
            ];
            
            $token          = JWTAuth::attempt($credentials);
            $user           = Auth::user()->toArray();
            $userData       = array_merge($user, ['token' => $token]);  
            $responseData   = $this->userTransformer->transform((object)$userData);

            Connections::create([
                'user_id'           => $user['id'],
                'other_user_id'     => 1,
                'requested_user_id' => $user['id'],
                'is_accepted'       => 1
            ]);

            return $this->successResponse($responseData);
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }
    
    /**
     * Forgot Password
     *
     * @param Request $request
     * @return string
     */
    public function forgotpassword(Request $request)
    {
        if($request->get('email'))
        {
            $userObj = new User;

            $user = $userObj->where('email', $request->get('email'))->first();

            if($user)
            {
                $password       = str_random(6);
                $user->password = bcrypt($password);
                if($user->save() && 1 == 2 )  
                {
                    $to = $user->email;
                    $subject = "Reset Password - Gratitude";

                    $message = "
                    <html>
                    <head>
                    <title>Reset Password Gratitude App</title>
                    </head>
                    <body>
                    <p>
                        Hello $user->name,
                    </p>
                    <p>
                     Please use this password for Login <strong>$password </strong> Let us know if you have any concern.
                    </p>
                    <p>
                    Spottr
                    
                    </p>
                    </body>
                    </html>
                    ";

                    // Always set content-type when sending HTML email
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                    // More headers
                    $headers .= 'From: <info@spottrmedia.com>' . "\r\n";
                    if(mail($to, $subject, $message, $headers))
                    {
                        $successResponse = [
                            'message' => 'Reset Password Mail send successfully.'
                        ];
                    }
                }

                // Need to Remove
                $successResponse = [
                    'message' => 'Reset Password Mail send successfully.'
                ];
                return $this->successResponse($successResponse);
            }

            return $this->setStatusCode(400)->failureResponse([
                'error' => 'User not Found !'
            ], 'Something went wrong !');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Get User Profile
     * 
     * @param Request $request
     * @return json
     */
    public function getUserProfile(Request $request)
    {
        if($request->get('user_id'))
        {
            $userObj            = new User;
            $connectionModel    = new Connections;

            $user           = $userObj->with([
                'posts', 'post_requests', 'user_posts', 'connections', 'user_notifications', 'my_connections', 'accepted_connections'
            ])->find($request->get('user_id'));
            $userInfo       = $this->getAuthenticatedUser();
            
            if($user)
            {
                $responseData = $this->userTransformer->userInfo($user);
                
                return $this->successResponse($responseData);
            }

            return $this->setStatusCode(400)->failureResponse([
                'error' => 'User not Found !'
            ], 'Something went wrong !');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');     
    }

    /**
     * Update User Profile
     * 
     * @param Request $request
     * @return json
     */
    /*public function updageUserProfile(Request $request)
    {
        $headerToken = request()->header('Authorization');

        if($headerToken)
        {
            $token      = explode(" ", $headerToken);
            $userToken  = $token[1];
        }
        
        $userInfo   = $this->getApiUserInfo();
        $repository = new UserRepository;
        $input      = $request->all();
        
        if($request->file('profile_pic'))
        {
            $imageName  = rand(11111, 99999) . '_user.' . $request->file('profile_pic')->getClientOriginalExtension();
            if(strlen($request->file('profile_pic')->getClientOriginalExtension()) > 0)
            {
                $request->file('profile_pic')->move(base_path() . '/public/uploads/user/', $imageName);
                $input = array_merge($input, ['profile_pic' => $imageName]);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
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

        $status = $repository->updateUserStub($userInfo['userId'], $input);

        if($status)
        {
            $userObj = new User;

            $user = $userObj->find($userInfo['userId']);

            if($user)
            {
                $responseData = $this->userTransformer->updateUser($user);
                
                return $this->successResponse($responseData);
            }

            return $this->setStatusCode(400)->failureResponse([
                'error' => 'User not Found !'
            ], 'Something went wrong !');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');     
    }*/

    /**
     * Change Password
     * 
     * @param Request $request
     * @return string
     */
    public function changePassword(Request $request)
    {
        if($request->has('password') && $request->has('old_password'))
        {   
            $userInfo = $this->getAuthenticatedUser();
            $credentials = [
                'email'     => $userInfo->email,
                'password'  => $request->get('old_password')
            ];

            if(! Auth::attempt($credentials))
            {
                return $this->setStatusCode(200)->failureResponse([
                    'reason' => 'Invalid Old Password'
                ], 'Invalid Old Password !');
            }

            $userInfo->password = bcrypt($request->get('password'));

            if ($userInfo->save()) 
            {
                event(new UserPasswordChanged($userInfo));

                $successResponse = [
                    'message' => 'Password Updated successfully.'
                ];
            
                return $this->successResponse($successResponse);
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    public function updageUserPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password'  => 'required',
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
        
        $userInfo   = $this->getApiUserInfo();
        $user       = User::find($userInfo['userId']);

        $user->password = bcrypt($request->get('password'));

        if ($user->save())
        {
            $successResponse = [
                'message' => 'Password Updated successfully.'
            ];
            
            return $this->successResponse($successResponse, 'Password Updated successfully.');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Update User Profile
     * 
     * @param Request $request
     * @return json
     */
    public function updageUserProfile(Request $request)
    {
        $headerToken = request()->header('Authorization');

        if($headerToken)
        {
            $token      = explode(" ", $headerToken);
            $userToken  = $token[1];
        }
        
        $userInfo   = $this->getApiUserInfo();
        $repository = new UserRepository;
        $input      = $request->all();
        
        if($request->file('profile_pic'))
        {
            $imageName  = rand(11111, 99999) . '_user.' . $request->file('profile_pic')->getClientOriginalExtension();
            if(strlen($request->file('profile_pic')->getClientOriginalExtension()) > 0)
            {
                $request->file('profile_pic')->move(base_path() . '/public/uploads/user/', $imageName);
                $input = array_merge($input, ['profile_pic' => $imageName]);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
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

        $status = $repository->updateUserStub($userInfo['userId'], $input);

        if($status)
        {
            $userObj = new User;

            $user = $userObj->find($userInfo['userId']);

            if($user)
            {
                $responseData = $this->userTransformer->userInfo($user);
                
                return $this->successResponse($responseData);
            }

            return $this->setStatusCode(400)->failureResponse([
                'error' => 'User not Found !'
            ], 'Something went wrong !');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');     
    }


    /**
     * Validate User
     * @param  Request $request
     * @return json
     */
    public function validateUser(Request $request) 
    {
        if($request->has('username'))
        {
            $phone = $request->has('phone') ? $request->get('phone') : false;


            $email = $request->has('email') ? $request->get('email') : false;

            if($phone && $email)
            {
                $user = User::where('username', $request->get('username'))
                    ->orWhere('phone', $phone)
                    ->orWhere('email', $email)
                    ->first();
            }
            else if ($phone)
            {
                $user = User::where('username', $request->get('username'))
                ->orWhere('phone', $phone)
                ->first();
            }
            else if ($email)
            {
                $user = User::where('username', $request->get('username'))
                ->orWhere('email', $email)
                ->first();
            }
            else
            {
                $user = User::where('username', $request->get('username'))->first();
            }

            if(isset($user) && isset($user->id))
            {
                return $this->setStatusCode(400)->failureResponse([
                    'reason' => 'User exist with Username or Phone Number!'
                ], 'User exist with Username or Phone Number');
            }
            else
            {
                $successResponse = [
                    'message' => 'No User found ! Continue for Signup.'
                ];

                return $this->successResponse($successResponse);
            }

        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Input'
        ], 'Invalid Input');
    }

    public function testNotification(Request $request)
    {
        $text       = 'This is Test Push Notification';
        $payload    = [
            'mtitle' => '',
            'mdesc'  => $text,
        ];
                    
        if($request->get('device_token'))
        {
            PushNotification::iOS($payload, $request->get('device_token'));
            $successResponse = [
                    'message' => 'Push Notification Done'
            ];

            return $this->successResponse($successResponse);
        }

        PushNotification::iOS($payload, '4f224e9fae894057074cb1a20682bd665f8bcb57');
            $successResponse = [
                    'message' => 'Push Notification Done to Default Device'
            ];

        return $this->successResponse($successResponse);
    }
}