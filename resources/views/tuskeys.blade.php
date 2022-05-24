@extends('laravel-filemanager::_browse')

@section('content')

@php

$n = count($orphaned_tuskeys);
$title = "{$n} orphaned tus-key";
if($n != 1) $title .= 's';

$n2 = $total_keys;
$subtitle = "{$n2} ".($n2 != 1 ? "keys" : "key")." total";

@endphp

<div class='box-browse'>
    <hgroup>
        <h1>{{ $title }}</h1>
        <h2>{{ $subtitle }}</h2>
    </hgroup>
    @if (count($orphaned_tuskeys) > 0)
    <ul class='list-files'>
        @foreach($orphaned_tuskeys as $path => $file)
        @include('laravel-filemanager::_tuskey')
        @endforeach
    </ul>
    @else
    <p>Nice!</p>
    @endif
</div>
@stop