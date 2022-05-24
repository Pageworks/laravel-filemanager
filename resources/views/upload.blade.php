@extends('laravel-filemanager::_body')

@section('head')
<title>Uploader</title>

<!-- uppy style -->
<link href="https://releases.transloadit.com/uppy/v2.9.5/uppy.min.css" rel="stylesheet">
<style>
.uppy-dashboard {
    width:600px;
    margin:0 auto;
}
</style>

@stop

@section('content')
<div class="container pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center"><h5>Upload Test</h5></div>
                <div class="card-body">
                    <div class="text-center">API Token: <input type="text" id="input-token" size="50" style="border:1px solid;border-radius:3px;margin-bottom:15px;"></div>
                    <div class='uppy-dashboard'></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery: -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<!-- Uppy: -->
<script src="https://releases.transloadit.com/uppy/v2.9.5/uppy.min.js"></script>
<script>
    const upz = new Uppy.Core({debug:true}).use(Uppy.Dashboard, {
        debug:true,
        inline: true,
        target: '.uppy-dashboard',
        showProgressDetails: true,
        height: 400,
        browserBackButtonClose: false,
        restrictions: {
            maxFileSize: 1000000 // ~10MB
        }
    }).use(Uppy.Tus, {
        endpoint: '{{ $baseUrl }}/tus?path={{$path}}',
        chunkSize: 10000000, // ~10MB
        headers:(file) => {
            const token = $("#input-token").val();
            console.log('headers updated...');
            return {
                'Accept': 'application/json',
                'Authorization': 'Bearer '+token
            }
        },
    });
</script>
@stop