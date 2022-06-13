<li class='file'>
<span class='bar'>
    <a class='label'>{{ $orphan_tuskey['key'] }}</a>
    <span><a class='bttn expand-file'>Details</a></span>
</span>
<span class="expand">
    <span class='tuskeys'>
        <span class='bttns'>
            <a href='{{ $baseUrl }}/uploads/remove/{{ $orphan_tuskey['key'] }}' class='bttn'>Remove upload key</a>
            <span class='clear'></span>
        </span>
        @foreach($orphan_tuskey as $field=>$value)
            @if (!is_array($value))
            <span class='keyvalue'><span>{{ $field }}</span><span>{{ $value }}</span></span>
            @endif
        @endforeach
    </span>
</span>
</li>