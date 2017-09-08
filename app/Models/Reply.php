<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
  //use SoftDeletes;
  protected $table = 'reply';
  protected $primaryKey = 'reply_id';
  public $incrementing = true;
  public $timestamps = true;
  protected $guarded = [];
  //protected $dates = ['deleted_at'];
}
