<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;
use Carbon\Carbon;

class NotificationsTransformer extends Transformer
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

        $item->user     = (object)$item->user;
        $item->to_user  = (object)$item->to_user;
        
        $dt     = Carbon::now();
        return [
            "notification_id"   => (int) $item->id,
            "user_id"           => (int) $item->user_id,
            "tagged_user_id"    => (int) $item->to_user_id,
            "post_id"           => (int) $item->post_id,
            "comment_id"        => (int) $item->comment_id,
            "notification_type" => $this->nulltoBlank($item->notification_type),
            "description"       => $item->description,
            "is_read"           => (int) $item->is_read,
            'name'              => $item->user->name,
            'other_user_name'   => $item->to_user->name,
            'profile_pic'       => isset($item->user->profile_pic) ? URL::to('/').'/uploads/user/' . $item->user->profile_pic : '',
            'created_at'        => Carbon::parse($item->created_at)->diffForHumans(null, true, false) . ' ago'
        ];
    }
}