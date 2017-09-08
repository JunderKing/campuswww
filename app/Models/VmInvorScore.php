<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VmInvorScore extends Model
{
  protected $table = 'vm_invor_score';
  protected $primaryKey = 'score_id';
  //public $incrementing = false;
  public $timestamps = true;
  protected $guarded = [];
  //protected $dates = ['deleted_at'];
}
