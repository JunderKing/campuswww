<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VmMeeting extends Model
{
  use SoftDeletes;
  protected $table = 'vm_meeting';
  protected $primaryKey = 'meet_id';
  public $incrementing = true;
  public $timestamps = true;
  protected $guarded = [];
  protected $dates = ['deleted_at'];
}
