<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class Post extends Model
{
        use HasFactory;

    protected $casts = [
        'attachments' => 'array'
    ];

    protected $fillable = [
        'post_id',
        'owner_id',
        'text',
        'link',
        'attachments',
        'is_publish',
        'is_hidden',
        'network',
        'type'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('H:i:s j F Y');
    }
    
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('H:i:s j F Y');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post', 'post_id');
    }
    public function scopeQueueCount($query, $category)
    {
        $user = User::select('id')->where('email', 'WyomingCPA@yandex.ru')->first();
        $queues_ids = $user->queuesPost->pluck('id')->toArray();
        $object = $query->where('is_publish', false)->where('is_hidden', false)->whereIn('id', $queues_ids)->orderBy('created_at', 'desc');
        $category_value = [$category];
        $category_ids = Category::whereIn('name', $category_value)->pluck('id')->toArray();
        $object->whereHas('categories', function ($query) use ($category_ids) {
            $query->whereIn('category_id', array_values($category_ids));
        });

        return $object->count();
    }
}
