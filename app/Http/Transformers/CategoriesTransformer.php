<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

class CategoriesTransformer extends Transformer
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
            'category_id'   => (int) $item->id,
            'title'         => $item->title,
            'icon'          => URL::to('/').'/uploads/categories/' . $item->icon 
        ];
    }
}