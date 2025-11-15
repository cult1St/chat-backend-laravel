<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'inviter_id',
        'invitee_id',
        'status'
    ];
}
