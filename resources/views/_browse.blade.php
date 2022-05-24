@extends('laravel-filemanager::_body')

@section('head')
<title>File Browser</title>

<style>
.uppy-dashboard {
    width:600px;
    margin:0 auto;
}
</style>

<!-- uppy style -->
<link href="https://releases.transloadit.com/uppy/v2.9.5/uppy.min.css" rel="stylesheet">
<link href="{{ asset('vendor/pageworks/laravel-filemanager/css/file-browser.css') }}" rel="stylesheet">

<!-- jQuery: -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
@stop

@section('footer')
<footer>
    <ul>
        <li><a href="{{ $baseUrl }}/browse">Browse</a></li>
        <li><a href="{{ $baseUrl }}/models">All orphaned models</a></li>
        <li><a href="{{ $baseUrl }}/uploads">All orphaned tus-keys</a></li>
    </ul>
</footer>
<script type="text/javascript">
$(function(){
    function isNameOkay(name){
        if(!name.match(/^[^\/\\]+$/)) return false;
        return true;
    }
    $(function(){
        $('.expand-file').click(function(e){
            $(this).parent().parent().parent().toggleClass('show');
        });
        $('.bttn.rename').click(function(e){
            let url = $(this).attr('data-url');
            const original_name = $(this).attr('data-name');
            let name = '';
            do {
                name = prompt("Enter new name:", original_name);
                if(name === null) return;
            } while(!isNameOkay(name));

            location.href = url + "&name=" + name;
        });
        $('.bttn.make-folder').click(function(e){
            const original_name = 'new folder';
            let name = '';
            do {
                name = prompt("Enter folder name:", original_name);
                if(name === null) return;
            } while(!isNameOkay(name));
            location.href = "{{ $baseUrl }}/make?path={{$path}}&name=" + name;
        });
    });
});
</script>
@stop