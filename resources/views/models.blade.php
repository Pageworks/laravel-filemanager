@extends('laravel-filemanager::_browse')

@section('content')

@php

$n = count($orphaned_models);
$title = "{$n} orphaned model";
if($n != 1) $title .= 's';

@endphp

<div class='box-browse'>
    <hgroup>
        <h1>{{ $title }}</h1>
    </hgroup>
    @if ($n > 0)
    <ul class='list-files'>
        @foreach($orphaned_models as $path => $model)
        @include('laravel-filemanager::_model')
        @endforeach
    </ul>
    @else
    <p>Nice!</p>
    @endif
</div>
@stop