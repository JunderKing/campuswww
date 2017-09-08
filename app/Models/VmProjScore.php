<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VmProjScore extends Model
{
  protected $table = 'vm_proj_score';
  protected $primaryKey = 'score_id';
  //public $incrementing = true;
  public $timestamps = true;
  protected $guarded = [];
  //protected $dates = ['deleted_at'];
}
