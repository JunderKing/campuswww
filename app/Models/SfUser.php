<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SfUser extends Model
{
  //use SoftDeletes;
  protected $table = 'sf_user';
  protected $primaryKey = 'user_id';
  public $incrementing = false;
  public $timestamps = true;
  protected $guarded = [];
  //protected $dates = ['deleted_at'];
}
