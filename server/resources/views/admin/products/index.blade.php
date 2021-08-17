@extends('layout')

@section('title', __('admin/products.index.title'))

@section('content')
    @livewire('admin.products.crud')
@endsection
