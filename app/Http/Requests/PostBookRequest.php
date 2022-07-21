<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostBookRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {   
        /**
         * Issue with digits,
         * when isbn set to digits:13 and user pass array to it then return error 500 with wired exception
         * fast fix is to use range of 1000000000000 (first 13 digits number) and 9999999999999 (last 13 digits number)
         */
        return [
            'isbn'=>'required|numeric|unique:books|max:9999999999999|min:1000000000000',
            'title'=>'required|string',
            'description'=>'required|string',
            'authors'=>'required|array',
            'authors.*'=>'required|exists:authors,id|distinct'   
        ];
    }
}
