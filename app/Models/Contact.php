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

    public function inviter(){
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function invitee(){
        return $this->belongsTo(User::class, 'invitee_id');
    }
}
