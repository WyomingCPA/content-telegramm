@extends('adminlte::page')

@section('title', 'Создать объект')

@section('content_header')
    <h1>Создать объект</h1>
@stop

@section('content')

<div class="row">
    <div class="col-md-6">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Форма создания</h3>
            </div>

            <form action="{{ route('group.store') }}" method="POST">
                @csrf

                <div class="card-body">

                    {{-- Поле Название группы --}}
                    <div class="form-group">
                        <label for="name-group">Name</label>
                        <input type="text" name="nameGroup" id="name-group" class="form-control" placeholder="Название группы">
                    </div>

                    {{-- Поле Url Groups --}}
                    <div class="form-group">
                        <label for="url-group">Url Groups</label>
                        <input type="text" name="urlGroup" id="url-group" class="form-control" placeholder="Url Groups">
                    </div>

                    {{-- Поле Slug --}}
                    <div class="form-group">
                        <label for="slug-group">Slug</label>
                        <input type="text" name="slugGroup" id="slug-group" class="form-control" placeholder="Slug">
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>

            </form>
        </div>

    </div>
</div>

@stop