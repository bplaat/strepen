@extends('layout')

@section('title', __('settings.title'))

@section('content')
    <h1 class="title">@lang('settings.title')</h1>

    @livewire('settings.change-details')

    @livewire('settings.change-avatar')

    @livewire('settings.change-password')
@endsection
