<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsMessage extends Model
{
    protected $fillable = ['chat_id', 'message_id', 'delete_after'];
}
