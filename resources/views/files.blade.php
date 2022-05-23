<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- uppy style -->
        <link href="https://releases.transloadit.com/uppy/v2.9.5/uppy.min.css" rel="stylesheet">

        <title>Files</title>
        <!-- Styles -->
        <style>
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}
            body {
                font-family: 'Nunito', sans-serif;
            }
            .DashboardContainer {
                width:600px;
                margin:0 auto;
            }
        </style>
        <style>
            .clear {
                clear:both;
                display:block;
            }
            .hidden {
                display:none;
            }
            .box-browse {
                border-radius:5px;
                border:solid #ccc 1px;
                background:#eee;
                margin:50px;
            }
            .box-browse hgroup {
                padding:20px;
            }
            .box-browse hgroup h1 {
                font-size:125%;
                font-weight:normal;
                margin-bottom:5px;
                margin:0;
            }
            .box-browse hgroup h2 {
                font-size:80%;
                font-weight:normal;
                margin-top:5px;
            }
            a.bttn, button.bttn {
                z-index:100;
                background:#ccc;
                padding:2px 5px;
                border-radius:3px;
                font-size:75%;
                text-decoration:none;
                border:1px solid rgba(0,0,0,.25);
                white-space: nowrap;
                margin:0 0 0 5px;
                cursor:pointer;
            }
            a.bttn.right, button.bttn.right {
                float:right;
            }
            a.bttn:hover {
                border:1px solid #000;
                background:#666;
                color:#fff;
                text-decoration:none;
            }
            ul.list-dirs, ul.list-files {
                margin:0;
                padding:0;
                list-style:none;
                overflow: hidden;
            }
            ul.list-dirs li, ul.list-files li {
                border-top:1px solid #ccc;
                padding:0;
                margin:0;
                clear:both;
            }
            li .bar {
                padding:0;
                margin:0;
                display:flex;
            }
            li .bar > * {
                padding:10px 5px;
            }
            li .bar > *:last-child {
                padding:10px 20px 10px 5px;
            }
            li .expand {
                display:none;
                border-top:solid 1px #999;
                padding:20px;
                background:#ddd;
                box-shadow:0 1px 2px rgba(0,0,0,.3) inset;
            }
            li.show .expand {
                display:flex;
                flex-direction: column;
            }
            li .bar a.label {
                padding:10px 20px;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
                display: block;
                flex-grow: 1;
            }
            a.label:hover {
                color:#3333BB;
                text-decoration: underline;
            }
            ul.list-dirs li:hover, ul.list-files li:hover {
                background:#ddd;
            }
            .meta, .model, .tuskeys {
                flex-grow: 1;
                min-width: 0;
            }
            span + .model, span + .tuskeys {
                border-top:1px solid #999;
                padding-top:20px;
                margin-top:15px;
            }
            .meta .bttns,
            .model .bttns,
            .tuskeys .bttns {
                float:right;
            }
            .keyvalue {
                display: flex;
                font-size:90%;
                line-height: 175%;
            }
            .keyvalue *:first-child {
                min-width:130px;
                max-width:130px;
                text-transform: uppercase;
                font-size:75%;
                text-align:right;
                padding-right:15px;
                font-weight:bold;
                color:#333;
            }
            .keyvalue *:last-child {
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
                /*
                direction: rtl;
                text-align: left;
                */
            }
            .DashboardContainer {
                width:600px;
                margin:0 auto;
            }
</style>
    </head>
    <body class="antialiased">
        @if (isset($list))
        <div class='box-browse'>
            <hgroup>
            <button class='bttn UppyModalOpenerBtn right'>Upload File</button>
            <button class='bttn MakeFolderBtn right'>Make Dir</button>
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
                                <a href='{{ $baseUrl }}/uploads/remove/{{ $file['tus_key'] }}' class='bttn right'>Remove upload key</a>
                                <span class='clear'></span>
                            </span>
                            <span class='keyvalue'><span>tus upload key</span><span>{{ $file['tus_key'] }}</span></span>
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
                    @foreach($list['orphaned_models'] as $model)
                    <li class='file'>
                    <span class='bar'>
                        <a class='label'>{{ $model['file_name'] }}</a>
                        <span><a class='bttn expand-file'>Details</a></span>
                    </span>
                    <span class="expand">
                        <span class='model'>
                            <span class='bttns'>
                                <a href='{{ $baseUrl }}/remove?id={{ $model['id'] }}' class='bttn'>Remove from DB</a>
                                <span class='clear'></span>
                            </span>
                            @foreach($model as $field=>$value)
                                @if (!is_array($value))
                                <span class='keyvalue'><span>{{ $field }}</span><span>{{ $value }}</span></span>
                                @endif
                            @endforeach
                        </span>
                    </span>
                    </li>
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
                    @foreach($list['orphaned_tuskeys'] as $key => $file)
                    <li class='file'>
                    <span class='bar'>
                        <a class='label'>{{ $file['name'] }}</a>
                        <span><a class='bttn expand-file'>Details</a></span>
                    </span>
                    <span class="expand">
                        <span class='tuskeys'>
                            <span class='bttns'>
                                <a href='{{ $baseUrl }}/uploads/remove/{{ $key }}' class='bttn'>Remove upload key</a>
                                <span class='clear'></span>
                            </span>
                            @foreach($file as $field=>$value)
                                @if (!is_array($value))
                                <span class='keyvalue'><span>{{ $field }}</span><span>{{ $value }}</span></span>
                                @endif
                            @endforeach
                        </span>
                    </span>
                    </li>
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
        
        <div class='DashboardContainer'></div>

        <!-- jQuery: -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
        <!-- Uppy: -->
        <script src="https://releases.transloadit.com/uppy/v2.9.5/uppy.min.js"></script>
        <script>
            const upz = new Uppy.Core({debug:true}).use(Uppy.Dashboard, {
                debug:true,
                inline: false,
                trigger: '.UppyModalOpenerBtn',
                target: '.DashboardContainer',
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

        <!-- Expand / Collapse -->
        <script>
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
                $('.bttn.MakeFolderBtn').click(function(e){
                    const original_name = 'new folder';
                    let name = '';
                    do {
                        name = prompt("Enter folder name:", original_name);
                        if(name === null) return;
                    } while(!isNameOkay(name));
                    location.href = "{{ $baseUrl }}/make?path={{$path}}&name=" + name;
                });
            });
        </script>

    </body>
</html>