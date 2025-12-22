@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Advert Post')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Посты</h3>
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
                    <th>Status</th>
                    <th>text</th>
                    <th>Actions</th>
                    <th>updated_at</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($posts as $post)
                <tr>
                    <td>
                        <input type="checkbox" name="ids[]" value="{{ $post->id }}" class="row-check">
                    </td>
                    <td>{{ $post->id }}</td>

                    <td><img src="{{ asset('storage/' . optional($post->attachments)['image'] ?? 'no') }}" class="elevation-2" width="100"></td>
                    <td>
                        <form action="{{ route('advertising.update-status', $post->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-warning">
                                {{ $post->is_publish ? 'Выключить' : 'Включить' }}
                            </button>
                        </form>
                    </td>
                    <td><a class="link" target="_blank" href="{{ $post->link }}">Посмотреть</a></td>
                    <td>
                        <a href="{{ route('advertising.edit', $post->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-pen"></i> Редактировать
                        </a>
                        <a href="{{ route('advertising.detail', $post->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Детали
                        </a>
                        {{-- Кнопка "Удалить" --}}
                        <form action="{{ route('advertising.delete', $post->id) }}"
                            method="POST"
                            style="display:inline-block">
                            @csrf
                            <button type="submit"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Удалить?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                    <td>{{ $post->updated_at }}</td>
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