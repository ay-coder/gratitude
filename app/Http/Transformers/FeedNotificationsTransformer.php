<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

class FeedNotificationsTransformer extends Transformer
{
    /**
     * Transform
     *
     * @param array $data
     * @return array
     */
    public function transform($item)
    {
        if(is_array($item))
        {
            $item = (object)$item;
        }

        return [
            "feednotificationsId" => (int) $item->id, "feednotificationsUserId" =>  $item->user_id, "feednotificationsFeedId" =>  $item->feed_id, "feednotificationsDescription" =>  $item->description, "feednotificationsNotificationType" =>  $item->notification_type, "feednotificationsIcon" =>  $item->icon, "feednotificationsIsRead" =>  $item->is_read, "feednotificationsCreatedAt" =>  $item->created_at, "feednotificationsUpdatedAt" =>  $item->updated_at, 
        ];
    }

    public function transformAllNotifications($items)
    {
        $response = [];

        if(isset($items) && count($items))
        {
            foreach($items as $item)
            {
                $response[] = [
                    'notification_id'   => (int) $item->id,
                    'user_id'           => (int) $item->user_id,
                    'from_user_id'      => (int) $item->from_user_id,
                    'feed_id'           => (int) $item->feed_id,
                    'notification_type' => $item->notification_type,
                    'description'       => $item->description,
                    'icon'              => URL::to('/').'/uploads/notifications/' . $item->icon,
                    'is_read'           => (int) $item->is_read,
                    'created_at'        => date('m/d/Y H:i:s', strtotime($item->created_at))
                ];
            }
        }
        return $response;
    }
}