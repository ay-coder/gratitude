<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class StudentsTransformer extends Transformer
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
            "studentsId" => (int) $item->id, "studentsName" =>  $item->name, "studentsAge" =>  $item->age, "studentsStd" =>  $item->std, 
        ];
    }
}