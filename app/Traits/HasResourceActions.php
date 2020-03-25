<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020-03-24
 * Time: 下午 2:14
 */

namespace App\Traits;


trait HasResourceActions
{
    /**
     * @return mixed
     */
    public function index()
    {
        return $this->grid()->paginate()->toJson();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        return $this->form()->store();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        return $this->form()->update($id);
    }
}