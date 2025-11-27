<?php

namespace App\Http\Controllers\group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Group;
use App\Models\Source;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $models = Group::paginate(20);
        return view('group.index', ['groups' => $models,]);
    }
    public function create(Request $request)
    {
        return view('group.create', []);
    }
    public function store(Request $request)
    {
        $name = $request->input('nameGroup');
        $url_group = $request->input('urlGroup');
        $slug_group = $request->input('slugGroup');

        $model = new Group();
        $model->group = $name;
        $model->url_group = $url_group;
        $model->slug = $slug_group;
        $model->save();

        return back()->with('success', 'Создано!');
    }

    public function updateStatus($id)
    {
        $model = Group::findOrFail($id);
        if (!isset($model->is_start) || $model->is_start == false) {
            $model->is_start = true;
        } else {
            $model->is_start = false;
        }
        $model->save();

        return redirect()->back()->with('success', 'Статус обновлён');
    }
    public function updateStatusSource($id)
    {
        $model = Source::findOrFail($id);
        if (!isset($model->is_parce) || $model->is_parce == false) {
            $model->is_parce = true;
        } else {
            $model->is_parce = false;
        }
        $model->save();

        return redirect()->back()->with('success', 'Статус обновлён');
    }

    public function source($id)
    {
        $models = Source::where('groups_id', $id)->orderBy('updated_at', 'asc');
        return view('group.source', ['models' => $models->paginate(), 'id' => $id]);
    }
    public function sourceStore(Request $request, $id)
    {
        $id_group = $id;
        $name = $request->get('nameSource');
        $url_source = $request->get('urlSource');
        $owner_id = $request->get('ownerId');

        $model = new Source();
        $model->groups_id = $id_group;
        $model->name = $name;
        $model->url_source = $url_source;
        $model->owner_id = $owner_id;
        $model->save();

        return redirect()->route('group.source', ['id' => $id_group]);
    }

    public function delete($id)
    {
        Group::findOrFail($id)->delete();
        return back()->with('success', 'Удалено');
    }
    public function deleteSource($id)
    {
        Source::findOrFail($id)->delete();
        return back()->with('success', 'Удалено');
    }
}
