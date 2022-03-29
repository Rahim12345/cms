<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOptionRequest extends FormRequest
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
            'facebook'=>'required|max:255',
            'twitter'=>'required|max:255',
            'youtube'=>'required|max:255',
            'email'=>'required|max:255',
            'tel'=>'required|max:255',
            
        ];
    }

    public function attributes()
    {
        return [
            'facebook'=>'Facebook',
            'twitter'=>'Twitter',
            'youtube'=>'YouTube',
            'email'=>'Email',
            'tel'=>'Telefon',
            
        ];
    }
}


        