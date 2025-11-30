<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $favorite_sexy_post_count  = Post::queueCount('sexy');
        $favorite_anime_post_count = Post::queueCount('anime');

        $user = User::select('id')->where('email', 'WyomingCPA@yandex.ru')->first();
        $favorite_ids = $user->queuesPost->pluck('id')->toArray();

        $favorite_sexy_tumblr_photo_count = Post::where('is_publish', false)
            ->where('owner_id', 213)
            ->where('type', 'photo')
            ->where('network', 'tumblr')
            ->where('is_hidden', false)
            ->whereIn('id', $favorite_ids)
            ->orderBy('created_at', 'desc')->count();

        $favorite_anime_tumblr_photo_count = Post::where('is_publish', false)
            ->where('owner_id', 313)
            ->where('type', 'photo')
            ->where('network', 'tumblr')
            ->where('is_hidden', false)
            ->whereIn('id', $favorite_ids)
            ->orderBy('created_at', 'desc')->count();


        return view('dashboard.index', [
            'favorite_sexy_post_count' => $favorite_sexy_post_count,
            'favorite_anime_post_count' => $favorite_anime_post_count,
            'favorite_sexy_tumblr_photo_count' => $favorite_sexy_tumblr_photo_count,
            'favorite_anime_tumblr_photo_count' => $favorite_anime_tumblr_photo_count,
        ]);
    }
}
