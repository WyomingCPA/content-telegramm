<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Source extends Model
{
    use HasFactory;
    protected $table = 'sources';

    protected $fillable = ['groups_id', 'name', 'url_source', 'owner_id', 'is_parce'];

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('H:i:s j F Y');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('H:i:s j F Y');
    }
}
