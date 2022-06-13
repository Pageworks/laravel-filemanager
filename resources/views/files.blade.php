@extends('laravel-filemanager::_browse')

    @section('content')

        @if (isset($list))
        <div class='box-browse'>
            <hgroup>
            <button class='bttn right open-uppy'>Upload File</button>
            <button class='bttn right make-folder'>Make Dir</button>
            <h1>{{$path}}</ h1>
            <h2>{{$pathAbs}}</h2>
            </hgroup>

            <ul class='list-dirs'>
            @foreach ($list['dirs'] as $dir)
                <li>
                    <span class='bar'>
                        <a href='{{ $baseUrl }}/browse?{{ $dir['lookup'] }}' class='label'>{{ $dir['name'] }}</a>
                        @if ($dir['name'] != '..')
                        <span><a class='bttn expand-file'>Details</a></span>
                        @endif
                    </span>
                    @if ($dir['name'] != '..')
                    <span class="expand">
                        <span class='bttns'>
                            <a class='bttn rename right' data-name='{{ $dir['name'] }}' data-url='{{ $baseUrl }}/rename?{{ $dir['lookup'] }}'>Rename</a>
                            <a href='{{ $baseUrl }}/delete?{{ $dir['lookup'] }}' class='bttn right'>Delete</a>
                            <span class='clear'></span>
                        </span>
                        <span class='meta'>
                            <span class='keyvalue'><span>OS file owner</span><span>{{ $dir['owner_name'] }} ( {{ $dir['owner_id'] }} )</span></span>
                            <span class='keyvalue'><span>Permissions</span><span>{{ $dir['permissions'] }}</span></span>
                        </span>
                    </span>
                    @endif
                </li>
            @endforeach
            </ul>

            <ul class='list-files'>
            @foreach($list['files'] as $file)
                <li class='file'>
                    <span class='bar'>
                        <a href='{{ $baseUrl }}/download?{{ $file['lookup'] }}' class='label'>{{ $file['name'] }}</a>
                        <span class='size'>{{ $file['size'] }}</span>
                        <span><a class='bttn expand-file'>Details</a></span>
                    </span>
                    <span class="expand">
                        <span class='meta'>
                            <span class='bttns'>
                                <a class='bttn rename right' data-name='{{ $file['name'] }}' data-url='{{ $baseUrl }}/rename?{{ $file['lookup'] }}'>Rename</a>
                                <a href='{{ $baseUrl }}/delete?{{ $file['lookup'] }}' class='bttn right'>Delete</a>
                                <span class='clear'></span>
                            </span>
                            <span class='keyvalue'><span>Filename</span><span>{{ $file['name'] }}</span></span>
                            <span class='keyvalue'><span>Relative path</span><span>{{ $file['location_rel'] }}</span></span>
                            <span class='keyvalue'><span>Absolute path</span><span>{{ $file['location_abs'] }}</span></span>
                            <span class='keyvalue'><span>File size</span><span>{{ $file['size'] }} ( {{ $file['bytes'] }} bytes )</span></span>
                            <span class='keyvalue'><span>OS file owner</span><span>{{ $file['owner_name'] }} ( {{ $file['owner_id'] }} )</span></span>
                            <span class='keyvalue'><span>Permissions</span><span>{{ $file['permissions'] }}</span></span>

                            <span class='keyvalue'><span>Accessed</span><span>{{ date('h:i a \o\n l, F d Y', $file['atime']) }}</span></span>
                            <span class='keyvalue'><span>Modified</span><span>{{ date('h:i a \o\n l, F d Y', $file['mtime']) }}</span></span>
                            <span class='keyvalue'><span>Changed</span><span>{{ date('h:i a \o\n l, F d Y', $file['ctime']) }}</span></span>
                        </span>
                        <span class='model'>
                            <span class='bttns'>
                                @if (array_key_exists('model', $file))
                                <a href='{{ $baseUrl }}/remove?{{ $file['lookup'] }}' class='bttn right'>Remove from DB</a>
                                @else
                                <a href='{{ $baseUrl }}/add?{{ $file['lookup'] }}' class='bttn right'>Add to DB</a>
                                @endif
                                <span class='clear'></span>
                            </span>
                            @if (array_key_exists('model', $file))
                            @foreach($file['model'] as $field=>$value)
                            <span class='keyvalue'><span>{{ $field }}</span><span>{{ $value }}</span></span>
                            @endforeach
                            @endif
                        </span>
                        @if (array_key_exists('tus_key', $file))
                        <span class='tuskeys'>
                            <span class='bttns'>
                                <a href='{{ $baseUrl }}/uploads/remove/{{ $file['tus_key']['key'] }}' class='bttn right'>Remove upload key</a>
                                <span class='clear'></span>
                            </span>
                            <span class='keyvalue'><span>tus upload key</span><span>{{ $file['tus_key']['key'] }}</span></span>
                        </span>
                        @endif
                    </span>
                </li>
            @endforeach
            </ul>
            </div>
            @if (count($list['orphaned_models']) > 0)
            <div class='box-browse'>
                <hgroup>
                    <h1>Orphaned models</h1>
                </hgroup>
                <ul class='list-files'>
                    @foreach($list['orphaned_models'] as $orphan_model)
                        @include('laravel-filemanager::_model')
                    @endforeach
                </ul>
            </div>
            @endif
            @if (count($list['orphaned_tuskeys']) > 0)
            <div class='box-browse'>
                <hgroup>
                    <h1>Orphaned tus-keys</h1>
                </hgroup>
                <ul class='list-files'>
                    @foreach($list['orphaned_tuskeys'] as $orphan_tuskey)
                        @include('laravel-filemanager::_tuskey')
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        @else
        <div class='box-browse'>
            <hgroup>
            <h1>Directory not found</h1>
            <div><a href='{{ $baseUrl }}/browse' class='bttn'>Back to /</a></div>
            </hgroup>
        </div>
        @endif
        
        <div class='uppy-dashboard'></div>

        <!-- Uppy: -->
        <script src="https://releases.transloadit.com/uppy/v2.9.5/uppy.min.js"></script>
        <script>
            const upz = new Uppy.Core({debug:true}).use(Uppy.Dashboard, {
                debug:true,
                inline: false,
                trigger: '.bttn.open-uppy',
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
                    return {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer '+token
                    };
                },
            });
        </script>
    @stop