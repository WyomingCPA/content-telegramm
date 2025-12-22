@extends('adminlte::page')

@section('title', 'Редактировать рекламный пост')

@section('content_header')
    <h1>Редактировать рекламный пост</h1>
@stop

@section('content')

<form action="{{ route('advertising.update', $post->id) }}"
      method="POST"
      enctype="multipart/form-data">
    @csrf

    {{-- тип поста --}}
    <input type="hidden" name="type" value="ad">

    <div class="card card-warning">
        <div class="card-body">

            {{-- Текст --}}
            <div class="form-group">
                <label>Текст рекламного поста</label>
                <textarea name="text"
                          class="form-control"
                          rows="6"
                          maxlength="2000"
                          required>{{ old('text', $post->text) }}</textarea>
            </div>

            {{-- Текущая картинка --}}
            @if(!empty($post->attachments['image']))
                <div class="form-group">
                    <label>Текущая картинка</label><br>
                    <img src="{{ asset('storage/'.$post->attachments['image']) }}"
                         style="max-height:200px"
                         class="img-thumbnail mb-2">
                </div>
            @endif

            {{-- Новая картинка --}}
            <div class="form-group">
                <label>Загрузить новую картинку (необязательно)</label>
                <input type="file"
                       name="image"
                       class="form-control-file"
                       accept="image/*">
            </div>

            {{-- Кнопка --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Текст кнопки</label>
                        <input type="text"
                               name="button_text"
                               class="form-control"
                               value="{{ old('button_text', $post->attachments['button_text'] ?? '') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ссылка кнопки</label>
                        <input type="url"
                               name="link"
                               class="form-control"
                               value="{{ old('link', $post->link) }}">
                    </div>
                </div>
            </div>

            {{-- Сеть --}}
            <div class="form-group">
                <label>Соцсеть</label>
                <select name="network" class="form-control">
                    <option value="telegramm" @selected($post->network === 'telegramm')>Telegram</option>
                    <option value="vk" @selected($post->network === 'vk')>VK</option>
                    <option value="tumblr" @selected($post->network === 'tumblr')>Tumblr</option>
                </select>
            </div>

            {{-- Активность --}}
            <div class="form-check">
                <input type="checkbox"
                       name="is_publish"
                       class="form-check-input"
                       value="1"
                       {{ $post->is_publish ? 'checked' : '' }}>
                <label class="form-check-label">Активен</label>
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-warning">
                💾 Обновить рекламный пост
            </button>

            <a href="{{ route('advertising.index') }}"
               class="btn btn-secondary float-right">
                Назад
            </a>
        </div>
    </div>
</form>

@stop