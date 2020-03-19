<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\AdminRequest;
use App\Models\Admin;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;

class AdminController extends BaseController
{

    public function index()
    {

        $users = QueryBuilder::for(Admin::class)
            ->allowedFilters(['name', 'user_name', 'email'])
            ->allowedSorts('id', 'name')
            ->paginate()
            ->toJson();

        return $users;
    }


    //create
    public function store(AdminRequest $request)
    {
        $data = $request->all();
        Admin::create(array_merge($data, [
            'password' => bcrypt($data['password'])
        ]));
        return response()->json();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    //update
    public function update(AdminRequest $request, Admin $admin)
    {
        $data = $request->only('name', 'email', 'password');
        if (Arr::get($data, 'password')) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $admin->update($data);
        return response()->json();
    }

    //delete
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            $id = explode(",", $id);
        }
        Admin::destroy(collect($id));
        return response()->json();
    }
}
