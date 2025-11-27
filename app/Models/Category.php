<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];
    protected $table = 'categories';
    
    public $timestamps = false;

    public function rssItems()
    {
        return $this->belongsToMany(RssItem::class);
    }

    public function postItems()
    {
        return $this->belongsToMany(Post::class);
    }
}
