@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Anime Telegram Reblog')


{{-- Content body: main page content --}}

@section('content_body')
@csrf
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Группы</h3>

    </div>

    <div class="card-body p-0">
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Group</th>
                    <th>List Source</th>
                    <th>Status</th>
                    <th>updated_at</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($groups as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->group }}</td>
                    <td>{{ $item->count_source }}
                        <a href="{{ route('group.source', $item->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Редактировать
                        </a>
                    </td>
                    <td>
                        <form action="{{ route('group.update-status', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-warning">
                                {{ $item->is_start ? 'Выключить' : 'Включить' }}
                            </button>
                        </form>
                    </td>
                    <td>{{ $item->updated_at }}</td>
                    <td>
                        {{-- Кнопка "Удалить" --}}
                        <form action="{{ route('group.delete', $item->id) }}"
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
        {{ $groups->links() }}
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

</script>
@endpush