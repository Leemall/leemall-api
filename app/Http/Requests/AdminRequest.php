<?php

namespace App\Http\Requests;

class AdminRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()){
            case 'POST':{
                return [
                    'user_name' => 'required|unique:admins',
                    'name' => 'required',
                    'email'=>'required|email|unique:admins',
                    'password'=>'required|min:6'
                ];
            }
            case 'PUT':{
                $admin = $this->route('admin');
                return [
                    'name' => 'required',
                    'email'=>'required|email|unique:admins,email,'.$admin->id,
                ];
            }
        }


    }

    public function attributes()
    {
        return [
            'user_name' => '用户名',
            'name'=>'姓名',
        ];
    }
}
