<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comnt extends Model
{
  //use SoftDeletes;
  protected $table = 'comnt';
  protected $primaryKey = 'comnt_id';
  public $incrementing = true;
  public $timestamps = true;
  protected $guarded = [];
  //protected $dates = ['deleted_at'];
}
