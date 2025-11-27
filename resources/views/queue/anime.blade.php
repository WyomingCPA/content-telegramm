@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Anime очередь постов из ВК')


{{-- Content body: main page content --}}

@section('content_body')
<form id="bulk-action-form" method="POST" action="">
    @csrf
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Посты</h3>
            <div class="mb-3">

                <button type="button" id="publish" class="btn btn-warning btn-sm">
                    <i class="fas fa-telegram"></i> Опубликовать
                </button>

                <button type="button" id="hidden" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Скрыть
                </button>

            </div>
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
                        <td><img src="{{ $post->attachments[0][1] }}" class="elevation-2" width="100"></td>
                        <td>{{ count((array)$post->attachments[0])-1 }}</td>
                        <td><a class="link" target="_blank" href="https://vk.com/{{ $post->link }}">Посмотреть</a></td>
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
        let checks = document.querySelectorAll('.row-check');
        checks.forEach(c => c.checked = this.checked);
    });

    function getSelectedIds() {
        let ids = [];
        document.querySelectorAll('.row-check:checked').forEach(c => {
            ids.push(c.value);
        });
        return ids;
    }

    // Универсальная отправка POST
    function sendAction(url) {
        let ids = getSelectedIds();

        if (ids.length === 0) {
            alert("Ничего не выбрано");
            return;
        }

        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                ids: ids
            })
        }).then(r => location.reload());
    }

    document.getElementById('publish').onclick = () =>
        sendAction("{{ route('vk.mass.anime.photo.publish') }}");

    document.getElementById('hidden').onclick = () =>
        sendAction("{{ route('queue.unset-queue') }}");
</script>
@endpush