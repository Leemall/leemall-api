<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\Api\ApiForm;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\QueryBuilder;

class RoleController extends BaseController
{
    protected function grid()
    {
        $grid = QueryBuilder::for(Role::class);
        $grid->allowedSorts('id')
            ->where('guard_name',$this->guard_name);
        return $grid;
    }

    protected function form()
    {
        $form = new ApiForm(new Role());
        $form->only(['name','guard_name']);
        $form->saving(function(ApiForm $form){
            $form->guard_name = $this->guard_name;
            if(!$form->model()->id){
                $form->model()->findOrCreate($form->name,'admin');
            }elseif($form->model()->guard_name == $this->guard_name){
                $form->model()->name = $form->name;
                $form->model()->save();
            }
            return response()->json();
        });
        return $form;
    }
}
