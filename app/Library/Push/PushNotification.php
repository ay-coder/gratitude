<?php 
namespace App\Library\Push;


class PushNotification 
{
	// (Android)API access key from Google API's Console.
	private static $API_ACCESS_KEY = 'AIzaSyDG3fYAj1uW7VB-wejaMJyJXiO5JagAsYI';

	// (iOS) Private key's passphrase.
	private static $passphrase = 'wve@123';
	
	// (Windows Phone 8) The name of our push channel.
    private static $channelName = "";
	
	// Change the above three vriables as per your app.

	public function __construct() 
	{
		exit('Init function is not allowed');
	}
	
    /**
     * Android
     * 
     * @param array $data
     * @param string $reg_id
     * @return bool|mixed
     */
	public static function android($data, $devicetoken) 
	{
	        $message = [
	        	/*'title' 		=> $data['mtitle'],
	        	'message' 		=> $data['mdesc'],
	        	'subtitle' 		=> '',
	        	'tickerText' 	=> '',
	        	'msgcnt' 		=> 1,
	        	'vibrate' 		=> 1*/
			    'title' 	=> $data['mtitle'],
                'body' 		=> $data['mdesc'],
                'badge'		=> isset($data['badgeCount']) ? $data['badgeCount'] : 0,
                'feed_id' 	=> isset($data['feed_id']) ? $data['feed_id'] : '',
                'feed_type' => isset($data['feed_type']) ? (string) $data['feed_type'] : '',
                'user_id' 	=> isset($data['user_id']) ? $data['user_id'] : '',
                'mtype' 	=> isset($data['mtype']) ? $data['mtype'] : '',
                'from_user_id' => isset($data['from_user_id']) ? $data['from_user_id'] : '',
                'to_user_id' => isset($data['to_user_id']) ? $data['to_user_id'] : '',
                'comment_id' 	=> isset($data['comment_id']) ? $data['comment_id'] : '',
                'tagged_user_id' 	=> isset($data['tagged_user_id']) ? $data['tagged_user_id'] : '',
	        ];
	        
	        /*$headers = [
	        	'Authorization: key=' .self::$API_ACCESS_KEY,
	        	'Content-Type: application/json'
	        ];

	        $fields = [
	            'registration_ids' 	=> array($reg_id),
	            'data' 				=> $message,
	            'badge'				=> access()->getUnreadNotificationCount(),
	        ];
	
	    	return $this->useCurl($url, $headers, json_encode($fields));*/


			$fields = array
			(
				'registration_ids' 	=> array($devicetoken),
			    'data'      		=> $message
			);

			$key = 'AAAALisBbZY:APA91bHR_V5SB6F1z_UUahsuuX2yGpuI0GS1yJIt50GXieZWt8hz6TLLuKj1DMDR_3-UHIMCSru9QXK40s367mXh2zCzId177lzCmp1-C_sKjf7GZijuFyFq-2l42vGHU6CHumOPb-cH';
			 
			$headers = array
			(
			    'Authorization: key='.$key,
			    'Content-Type: application/json'
			);
			 
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );
			curl_close( $ch );
		
			return $result;

    	}
	
	/**
	 * Window Phone
	 * @param array $data
	 * @param string $uri
	 */
	public function WP($data, $uri) 
	{
		$delay = 2;
		$msg =  "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
		        "<wp:Notification xmlns:wp=\"WPNotification\">" .
		            "<wp:Toast>" .
		                "<wp:Text1>".htmlspecialchars($data['mtitle'])."</wp:Text1>" .
		                "<wp:Text2>".htmlspecialchars($data['mdesc'])."</wp:Text2>" .
		            "</wp:Toast>" .
		        "</wp:Notification>";
		
		$sendedheaders =  array(
		    'Content-Type: text/xml',
		    'Accept: application/*',
		    'X-WindowsPhone-Target: toast',
		    "X-NotificationClass: $delay"
		);
		
		$response = $this->useCurl($uri, $sendedheaders, $msg);
		
		$result = array();
		foreach(explode("\n", $response) as $line) {
		    $tab = explode(":", $line, 2);
		    if (count($tab) == 2)
		        $result[$tab[0]] = trim($tab[1]);
		}
		
		return $result;
	}
	
    /**
     * iOS
     * 
     * @param array $data
     * @param string $devicetoken
     * @return bool|mixed
     */
	public static function iOS($data, $devicetoken) 
	{
		$deviceToken = $devicetoken;

		$ctx = stream_context_create();

		// ck.pem is your certificate file
		stream_context_set_option($ctx, 'ssl', 'local_cert', public_path().DIRECTORY_SEPARATOR.'live-grati.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', self::$passphrase);

		//LIVE URL - gateway.push.apple.com
		//Sandbox - gateway.sandbox.push.apple.com
		// Open a connection to the APNS server
		$fp = stream_socket_client(
			'ssl://gateway.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);

		// Create the payload body
		$body['aps'] = array(
			'alert' => array(
			    'title' 	=> $data['mtitle'],
                'body' 		=> $data['mdesc'],
                'feed_id' 	=> isset($data['feed_id']) ? (string) $data['feed_id'] : '',
                'feed_type' => isset($data['feed_type']) ? (string) $data['feed_type'] : '',
                'user_id' 	=> isset($data['user_id']) ? $data['user_id'] : '',
                'mtype' 	=> isset($data['mtype']) ? $data['mtype'] : '',
                'from_user_id' => isset($data['from_user_id']) ? $data['from_user_id'] : '',
                'to_user_id' => isset($data['to_user_id']) ? $data['to_user_id'] : '',
                'comment_id' 	=> isset($data['comment_id']) ? $data['comment_id'] : '',
                'tagged_user_id' 	=> isset($data['tagged_user_id']) ? $data['tagged_user_id'] : '',

            ),
            'badge'	=> isset($data['badgeCount']) ? $data['badgeCount'] : 0,
			'sound' => 'default'
		);

		// Encode the payload as JSON
		$payload = json_encode($body);

		// Build the binary notification
		@$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

		// Send it to the server
		$result = @fwrite($fp, $msg, strlen($msg));

		// Close the connection to the server
		@fclose($fp);

		if (!$result)
			return 'Message not delivered' . PHP_EOL;
		else
			return 'Message successfully delivered' . PHP_EOL;
	}
	
	/**
	 * useCurl
	 * 
	 * @param object &$model
	 * @param string $url
	 * @param array $headers
	 * @param array $fields
	 * @return bool|mixed
	 */
	private function useCurl(&$model, $url, $headers, $fields = null) 
	{
	        // Open connection
	        $ch = curl_init();
	        if ($url) {
	            // Set the url, number of POST vars, POST data
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_POST, true);
	            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	     
	            // Disabling SSL Certificate support temporarly
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	            if ($fields) {
	                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	            }
	     
	            // Execute post
	            $result = curl_exec($ch);
	            if ($result === FALSE) {
	                die('Curl failed: ' . curl_error($ch));
	            }
	     
	            // Close connection
	            curl_close($ch);
	
	            return $result;
        }
    }
}
?>