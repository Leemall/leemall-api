<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Model
{
    use HasRoles;
    protected $fillable = ['user_name','name','password','email','phone','avatar','last_login','last_login_ip','enabled'];
}
