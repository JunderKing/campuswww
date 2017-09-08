<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VmUser extends Model
{
  protected $table = 'vm_user';
  protected $primaryKey = 'user_id';
  public $incrementing = false;
  public $timestamps = true;
  protected $guarded = [];
  //protected $dates = ['deleted_at'];
}
