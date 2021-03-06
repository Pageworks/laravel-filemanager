<li class='file'>
<span class='bar'>
    <a class='label'>{{ $orphan_model['file_name'] }}</a>
    <span><a class='bttn expand-file'>Details</a></span>
</span>
<span class="expand">
    <span class='model'>
        <span class='bttns'>
            <a href='{{ $baseUrl }}/remove?id={{ $orphan_model['id'] }}' class='bttn'>Remove from DB</a>
            <span class='clear'></span>
        </span>
        @foreach($orphan_model as $field => $value)
            <span class='keyvalue'><span>{{ $field }}</span><span>{{ $value }}</span></span>
        @endforeach
    </span>
</span>
</li>