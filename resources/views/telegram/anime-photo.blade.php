@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Anime Telegram Reblog')


{{-- Content body: main page content --}}

@section('content_body')
<form id="bulk-action-form" method="POST" action="{{ route('telegram.anime-photo') }}">
    @csrf
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Посты</h3>
            {{-- Кнопка действий --}}
            <button type="submit" class="btn btn-success btn-sm" id="post-publish" disabled>
                Опубликовать в Телеграмм
            </button>
        </div>

        <div class="card-body p-0">
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        {{-- Чекбокс "Выбрать все" --}}
                        <th width="30">
                            <input type="checkbox" id="check-all">
                        </th>
                        <th>ID</th>
                        <th>attachments</th>
                        <th>count_attachments</th>
                        <th>text</th>
                        <th>created_at</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($posts as $post)
                    <tr>
                        <td>
                            <input type="checkbox" name="ids[]" value="{{ $post->id }}" class="row-check">
                        </td>
                        <td>{{ $post->id }}</td>
                        <td><img src="{{ $post->attachments[0] }}" class="elevation-2" width="100"></td>
                        <td>{{ count((array)$post->attachments[1]) }}</td>
                        <td><a class="link" target="_blank" href="{{ $post->link }}">Посмотреть</a></td>
                        <td>{{ $post->created_at }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Нет данных</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Пагинация --}}
        <div class="card-footer">
            {{ $posts->links() }}
        </div>
    </div>
</form>
@stop

{{-- Push extra CSS --}}

@push('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
<script>
    // Чекбокс "выбрать все"
    document.getElementById('check-all').addEventListener('change', function() {
        let checked = this.checked;
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = checked);
        toggleBulkButton();
    });

    // Кнопка активируется когда что-то выбрано
    document.querySelectorAll('.row-check').forEach(cb => {
        cb.addEventListener('change', toggleBulkButton);
    });

    function toggleBulkButton() {
        let selected = document.querySelectorAll('.row-check:checked').length;
        document.getElementById('post-publish').disabled = (selected === 0);
    }
</script>
@endpush