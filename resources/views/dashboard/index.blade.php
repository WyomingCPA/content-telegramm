@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Статистика</h1>
@stop

@section('content')
<div class="row">
    {{-- Очередь VK Anime --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $favorite_anime_post_count ?? 0 }}</h3>
                <p>Anime from VK</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <a href="{{ route('vk.anime-photo-all') }}" class="small-box-footer">
                Подробнее <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    {{-- Очередь VK Sexy --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $favorite_sexy_post_count ?? 0 }}</h3>
                <p>Sexy from VK</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <a href="{{ route('vk.sexy-photo-all') }}" class="small-box-footer">
                Подробнее <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    {{-- Очередь Tumblr Sexy Photo--}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $favorite_sexy_tumblr_photo_count ?? 0 }}</h3>
                <p>Sexy Photo from Tumblr</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <a href="{{ route('tumblr.sexy-photo-all') }}" class="small-box-footer">
                Подробнее <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    {{-- Очередь Tumblr Anime Photo--}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $favorite_anime_tumblr_photo_count ?? 0 }}</h3>
                <p>Anime Photo from Tumblr</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <a href="{{ route('tumblr.anime-photo-all') }}" class="small-box-footer">
                Подробнее <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

@stop