<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScUser extends Model
{
  //use SoftDeletes;
  protected $table = 'sc_user';
  protected $primaryKey = 'user_id';
  public $incrementing = false;
  public $timestamps = true;
  protected $guarded = [];
  //protected $dates = ['deleted_at'];
}
