@extends('layouts.app')

@section('title', __('errors.403.title'))

@section('content')
    <div class="content">
        <h1 class="title">@lang('errors.403.title')</h1>
        <p>@lang('errors.403.information', ['url' => $_SERVER['REQUEST_URI']])</p>
        <p>@lang('errors.403.help')</p>
    </div>
@endsection
