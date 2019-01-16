<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Access\User\User;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend.dashboard');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function sendPushNotifications(Request $request)
    {
    	if($request->has('notification_text'))
    	{
    		$users 	= User::all();
    		$text  	= $request->get('notification_text');
    		$sr 	= 0;

    		foreach($users as $user)
    		{
    			$payload  = [
    			    'mtitle'    => '',
    			    'mdesc'     => $text,
    			    'ntype'     => 'GENERAL_NOTIFICATION'
    			];

    			access()->sentPushNotification($user, $payload);

    			$sr++;
    		}

    		return redirect()->route('admin.push-notifications')->withFlashSuccess("Total ".$sr." Push Notification Send Successfully!");
    	}

		return view('backend.push-notification');
    }
}
