<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserForm extends Model
{
  protected $table = 'user_form';
  protected $primaryKey = 'record_id';
  public $incrementing = true;
  public $timestamps = true;
  protected $guarded = [];
}
