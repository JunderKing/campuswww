<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SfProjScore extends Model
{
  //use SoftDeletes;
  protected $table = 'sf_proj_score';
  protected $primaryKey = 'score_id';
  //public $incrementing = false;
  public $timestamps = true;
  protected $guarded = [];
  //protected $dates = ['deleted_at'];
    //
}
