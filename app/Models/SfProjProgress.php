<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SfProjProgress extends Model
{
  //use SoftDeletes;
  protected $table = 'sf_proj_progress';
  //protected $primaryKey = '';
  //public $incrementing = false;
  public $timestamps = true;
  protected $guarded = [];
  //protected $dates = ['deleted_at'];
}
