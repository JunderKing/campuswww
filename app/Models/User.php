<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  protected $table = 'user';
  protected $primaryKey = 'user_id';
  public $incrementing = true;
  public $timestamps = true;
  protected $guarded = [];
}
