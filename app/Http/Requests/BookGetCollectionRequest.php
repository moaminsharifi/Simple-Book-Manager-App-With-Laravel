<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookGetCollectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'integer|min:1',
            'sortColumn' => 'in:title,avg_review',
            'sortDirection' => 'in:ASC,DESC',
            'title'=> 'string',
            'authors'=> 'string',

        ];
    }
}
