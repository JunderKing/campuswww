<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostImage extends Model
{
  use SoftDeletes;
  protected $table = 'post_image';
  protected $primaryKey = 'post_id';
  public $incrementing = false;
  public $timestamps = true;
  protected $guarded = [];
  protected $dates = ['deleted_at'];
}
