@extends('adminlte::page')

@section('title', 'Items')

@section('content_header')
<h1>Добавление источников</h1>
@stop

@section('content')

{{-- Форма добавления --}}
<div class="card">
    <div class="card-body">

        <form action="{{ route('group.source-store', ['id' => $id]) }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-4">
                    <x-adminlte-input name="nameSource" label="Name Source" placeholder="Name Source" />
                </div>

                <div class="col-md-4">
                    <x-adminlte-input name="urlSource" label="Url Source" placeholder="Url Source" />
                </div>

                <div class="col-md-4">
                    <x-adminlte-input name="ownerId" label="Owner ID" />
                </div>
            </div>

            <x-adminlte-button class="mt-3" theme="primary" type="submit" label="Создать" />

        </form>

    </div>
</div>

{{-- Таблица записей --}}
<div class="card mt-4">
    <div class="card-header">Список записей</div>

    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>is_parce</th>
                    <th>updated_at</th>
                    <th style="width: 130px">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($models as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <form action="{{ route('group.update-status-source', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-warning">
                                {{ $item->is_parce ? 'Выключить' : 'Включить' }}
                            </button>
                        </form>
                    </td>
                    <td>{{ $item->updated_at }}</td>
                    <td>
                        {{-- Кнопка "Удалить" --}}
                        <form action="{{ route('group.delete-source', $item->id) }}"
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
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $models->links() }}
    </div>
</div>

@stop