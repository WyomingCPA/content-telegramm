<?php

namespace App\Http\Controllers\advert;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Post;


class AdvertController extends Controller
{
    public function create(Request $request)
    {
        return view('advertising.create', []);
    }
    public function index(Request $request)
    {
        $objects = Post::where('network', 'telegramm')
            ->where('type', 'ad')
            ->where('owner_id', 300)
            ->orderBy('created_at', 'desc');

        return view('advertising.index', [
            'posts' => $objects->paginate(20)
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'text'        => 'required|string|max:2000',
            'link'        => 'nullable|url',
            'button_text' => 'nullable|string|max:255',
            'image'       => 'nullable|image',
            'network'     => 'required',
            'type'        => 'required',
        ]);

        $attachments = [];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('ads', 'public');
            $attachments['image'] = $path;
        }

        if ($request->button_text) {
            $attachments['button_text'] = $request->button_text;
        }

        Post::create([
            'post_id'    => 0,
            'owner_id'   => 300,
            'text'       => $data['text'],
            'link'       => $data['link'],
            'attachments' => $attachments,
            'type'       => 'ad',
            'network'    => $data['network'],
            'is_publish' => $request->boolean('is_publish'),
        ]);

        return redirect()->route('advertising.index')
            ->with('success', 'Рекламный пост создан');
    }

    public function edit(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        return view('advertising.edit', [
            'post' => $post,
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'text'         => 'required|string|max:2000',
            'link'         => 'nullable|url',
            'button_text' => 'nullable|string|max:255',
            'image'        => 'nullable|image',
            'network'      => 'required',
            'type'         => 'required',
        ]);

        $attachments = $post->attachments ?? [];

        // новая картинка
        if ($request->hasFile('image')) {
            // удалить старую
            if (!empty($attachments['image'])) {
                Storage::disk('public')->delete($attachments['image']);
            }

            $attachments['image'] = $request
                ->file('image')
                ->store('ads', 'public');
        }

        // кнопка
        if ($request->button_text) {
            $attachments['button_text'] = $request->button_text;
        } else {
            unset($attachments['button_text']);
        }

        $updated = $post->update([
            'post_id'    => $post->post_id,
            'owner_id'   => $post->owner_id,
            'text'        => $data['text'],
            'link'        => $data['link'],
            'attachments' => $attachments,
            'network'     => $data['network'],
            'is_publish'  => $request->boolean('is_publish'),
        ]);

        return redirect()
            ->route('advertising.index')
            ->with('success', 'Рекламный пост обновлён');
    }
    public function delete($id)
    {
        Post::findOrFail($id)->delete();
        return back()->with('success', 'Удалено');
    }
    public function updateStatus($id)
    {
        $model = Post::findOrFail($id);
        if (!isset($model->is_publish) || $model->is_publish == false) {
            $model->is_publish = true;
        } else {
            $model->is_publish = false;
        }
        $model->save();

        return redirect()->back()->with('success', 'Статус обновлён');
    }
}
