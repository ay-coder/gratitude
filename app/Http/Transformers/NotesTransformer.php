<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class NotesTransformer extends Transformer
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
            "notesId" => (int) $item->id, "notesUserId" =>  $item->user_id, "notesNotes" =>  $item->notes, "notesCreatedAt" =>  $item->created_at, "notesUpdatedAt" =>  $item->updated_at, 
        ];
    }

    /**
     * Single Note Transform
     * 
     * @param object $item
     * @return array
     */
    public function singleNoteTransform($item)
    {
        $response = [
            'note_id'   => (int) $item->id,
            'notes'     => $item->notes,
            'created_at'    => date('m/d/Y H:i:s', strtotime($item->created_at))
        ];

        return $response;
    }

    public function transformAllNotes($items)
    {
        $response = [];

        if(isset($items) && count($items))   
        {
            foreach($items as $item)
            {
                $response[] = [
                    'note_id'       => (int) $item->id,
                    'notes'         => $item->notes,
                    'created_at'    => date('m/d/Y H:i:s', strtotime($item->created_at))
                ];
            }
        }

        return $response;
    }
}