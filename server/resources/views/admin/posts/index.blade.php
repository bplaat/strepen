@extends('layout')

@section('title', __('admin/posts.index.title'))

@section('content')
    @livewire('admin.posts.crud')
@endsection
