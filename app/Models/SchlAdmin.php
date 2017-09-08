<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchlAdmin extends Model
{
  //use SoftDeletes;
  protected $table = 'schl_admin';
  //protected $primaryKey = 'user_id';
  //public $incrementing = false;
  public $timestamps = true;
  protected $guarded = [];
  //protected $dates = ['deleted_at'];
}
