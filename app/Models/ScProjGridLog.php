<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScProjGridLog extends Model
{
  use SoftDeletes;
  protected $table = 'sc_proj_grid_log';
  //protected $primaryKey = '';
  //public $incrementing = false;
  public $timestamps = true;
  protected $guarded = [];
  protected $dates = ['deleted_at'];
}
