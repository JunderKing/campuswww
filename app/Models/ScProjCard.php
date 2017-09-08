<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScProjCard extends Model
{
  use SoftDeletes;
  protected $table = 'sc_proj_card';
  protected $primaryKey = 'card_id';
  public $incrementing = true;
  public $timestamps = true;
  protected $guarded = [];
  protected $dates = ['deleted_at'];
}
