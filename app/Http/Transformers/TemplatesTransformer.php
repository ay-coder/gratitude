<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class TemplatesTransformer extends Transformer
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
            "templatesId" => (int) $item->id, "templatesBody" =>  $item->body, "templatesCreatedAt" =>  $item->created_at, "templatesUpdatedAt" =>  $item->updated_at, 
        ];
    }
}