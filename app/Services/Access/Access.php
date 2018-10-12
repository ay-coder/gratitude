<?php

namespace App\Services\Access;

use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\ReadPost\ReadPost;
use App\Models\Connections\Connections;
use App\Models\FeedNotifications\FeedNotifications;
use App\Library\Push\PushNotification;

/**
 * Class Access.
 */
class Access
{
    /**
     * Get the currently authenticated user or null.
     */
    public function user()
    {
        return auth()->user();
    }

    /**
     * Return if the current session user is a guest or not.
     *
     * @return mixed
     */
    public function guest()
    {
        return auth()->guest();
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        return auth()->logout();
    }

    /**
     * Get the currently authenticated user's id.
     *
     * @return mixed
     */
    public function id()
    {
        return auth()->id();
    }

    /**
     * @param Authenticatable $user
     * @param bool            $remember
     */
    public function login(Authenticatable $user, $remember = false)
    {
        return auth()->login($user, $remember);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function loginUsingId($id)
    {
        return auth()->loginUsingId($id);
    }

    /**
     * Checks if the current user has a Role by its name or id.
     *
     * @param string $role Role name.
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if ($user = $this->user()) {
            return $user->hasRole($role);
        }

        return false;
    }

    /**
     * Checks if the user has either one or more, or all of an array of roles.
     *
     * @param  $roles
     * @param bool $needsAll
     *
     * @return bool
     */
    public function hasRoles($roles, $needsAll = false)
    {
        if ($user = $this->user()) {
            return $user->hasRoles($roles, $needsAll);
        }

        return false;
    }

    /**
     * Check if the current user has a permission by its name or id.
     *
     * @param string $permission Permission name or id.
     *
     * @return bool
     */
    public function allow($permission)
    {
        if ($user = $this->user()) {
            return $user->allow($permission);
        }

        return false;
    }

    /**
     * Check an array of permissions and whether or not all are required to continue.
     *
     * @param  $permissions
     * @param  $needsAll
     *
     * @return bool
     */
    public function allowMultiple($permissions, $needsAll = false)
    {
        if ($user = $this->user()) {
            return $user->allowMultiple($permissions, $needsAll);
        }

        return false;
    }

    /**
     * @param  $permission
     *
     * @return bool
     */
    public function hasPermission($permission)
    {
        return $this->allow($permission);
    }

    /**
     * @param  $permissions
     * @param  $needsAll
     *
     * @return bool
     */
    public function hasPermissions($permissions, $needsAll = false)
    {
        return $this->allowMultiple($permissions, $needsAll);
    }

    /**
     * Get ReadPost Ids
     * 
     * @param int $userId
     * @return array
     */
    public function getReadPostIds($userId = null)
    {
        if($userId)
        {
            return ReadPost::where('user_id', $userId)->pluck('post_id');
        }
        
        return [];
    }    

    /**
     * Get My ConnectionIds
     * 
     * @param int $userId
     * @return array
     */
    public function getMyConnectionIds($userId = null)
    {
        if($userId)   
        {
            $connectionModel        = new Connections;
            $myConnectionList       = $connectionModel->where([
                'user_id'       => $userId,
                'is_accepted'   => 1
            ])->pluck('other_user_id')->toArray();

            $otherConnectionList    = $connectionModel->where([
                'other_user_id' => $userId,
                'is_accepted'   => 1
            ])->pluck('requested_user_id')->toArray();

            $allConnections         = array_merge($myConnectionList, $otherConnectionList);
            $allConnections         = array_unique($allConnections);

            return $allConnections;
        }

        return [];
    }

    /**
     * Get My Request Ids
     * 
     * @param int $userId
     * @return array
     */
    public function getMyRequestIds($userId = null)
    {
        if($userId)   
        {
            $connectionModel        = new Connections;
            $myConnectionList       = $connectionModel->where([
                'user_id'       => $userId,
                'is_accepted'   => 0
            ])->pluck('other_user_id')->toArray();

            $otherConnectionList    = $connectionModel->where([
                'other_user_id' => $userId,
                'is_accepted'   => 0
            ])->pluck('requested_user_id')->toArray();

            $allConnections         = array_merge($myConnectionList, $otherConnectionList);
            $allConnections         = array_unique($allConnections);

            return $allConnections;
        }

        return [];
    }

    /**
     * Get My Request Ids
     * 
     * @param int $userId
     * @return array
     */
    public function getOnlyMyRequestIds($userId = null)
    {
        if($userId)   
        {
            $connectionModel        = new Connections;
            $myConnectionList       = $connectionModel->where([
                'user_id'           => $userId,
                'requested_user_id' => $userId,
                'is_accepted'       => 0
            ])->pluck('other_user_id')->toArray();

            $allConnections = array_unique($myConnectionList);

            return $allConnections;
        }

        return [];
    }

    /**
     * Get My Request Ids
     * 
     * @param int $userId
     * @return array
     */
    public function getOnlyReceiveRequestIds($userId = null)
    {
        if($userId)   
        {
            $connectionModel        = new Connections;
            $myConnectionList       = $connectionModel->where([
                'other_user_id'     => $userId,
                'is_accepted'       => 0
            ])
            ->pluck('user_id')->toArray();

            $allConnections = array_unique($myConnectionList);

            return $allConnections;
        }

        return [];
    }

    /**
     * Get Load More Flag
     * 
     * @param object $model
     * @return bool
     */
    public function getLoadMoreFlag($model = null)
    {

    }


    /**
     * Add Notification
     *
     * @param array $data
     */
    public function addNotification($data = array())
    {
        if(isset($data) && count($data))
        {
            return FeedNotifications::create($data);
        }

        return false;
    }


    /**
     * Sent Push Notification
     * 
     * @param object $user
     * @param array $payload
     * @return bool
     */
    public function sentPushNotification($user = null, $payload = null)
    {
        if($user && $payload)
        {
            if(isset($user->device_token) && strlen($user->device_token) > 4 && $user->device_type == 1)
            {
                PushNotification::iOS($payload, $user->device_token);
            }

            if(isset($user->device_token) && strlen($user->device_token) > 4 && $user->device_type == 0)
            {
                PushNotification::android($payload, $user->device_token);
            }
        }

        return true;
    }

    /**
     * Get Unread Notification Count
     * 
     * @param int $userId
     * @return int
     */
    public function getUnreadNotificationCount($userId = null)
    {
        if($userId)
        {
            return FeedNotifications::where([
                'is_read'   => 0,
                'user_id'   => $userId
            ])->count();
        }

        return 1;

    }
}
