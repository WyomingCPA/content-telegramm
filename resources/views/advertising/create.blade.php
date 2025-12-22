@extends('adminlte::page')

@section('title', 'Создать рекламный пост')

@section('content_header')
    <h1>Создать рекламный пост</h1>
@stop

@section('content')

<form action="{{ route('advertising.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Тип поста --}}
    <input type="hidden" name="type" value="ad">

    <div class="card card-primary">
        <div class="card-body">

            {{-- Текст --}}
            <div class="form-group">
                <label>Текст рекламного поста</label>
                <textarea name="text" class="form-control" rows="6" maxlength="2000" required>{{ old('text') }}</textarea>
            </div>

            {{-- Картинка --}}
            <div class="form-group">
                <label>Картинка</label>
                <input type="file" name="image" class="form-control-file" accept="image/*">
            </div>

            {{-- Кнопка --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Текст кнопки</label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ссылка кнопки</label>
                        <input type="url" name="link" class="form-control" value="{{ old('link') }}">
                    </div>
                </div>
            </div>

            {{-- Сеть --}}
            <div class="form-group">
                <label>Соцсеть</label>
                <select name="network" class="form-control">
                    <option value="telegramm">Telegram</option>
                    <option value="vk">VK</option>
                    <option value="tumblr">Tumblr</option>
                </select>
            </div>

            {{-- Активность --}}
            <div class="form-check">
                <input type="checkbox" name="is_publish" class="form-check-input" value="1" checked>
                <label class="form-check-label">Активен</label>
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                💾 Сохранить рекламный пост
            </button>
        </div>
    </div>
</form>

@stop