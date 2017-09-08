<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScProjRecord extends Model
{
  use SoftDeletes;
  protected $table = 'sc_proj_record';
  protected $primaryKey = 'rec_id';
  public $incrementing = true;
  public $timestamps = true;
  protected $guarded = [];
  protected $dates = ['deleted_at'];
}
