@extends('layout')

@section('title', __('home.title'))

@section('content')
    @auth
        <h1 class="title">@lang('home.header_auth', ['user.firstname' => Auth::user()->firstname])</h1>
    @else
        <h1 class="title">@lang('home.header_guest')</h1>
    @endauth
@endsection
