@php
    /**@var array[] $rows */
@endphp
@php
    $headers = collect($rows)->map(fn(array $row)=>array_keys($row))->flatten()->unique();

@endphp
<table>
    <thead>
    <tr>
        <th><span class="text-green-600">#</span></th>
        @foreach($headers as $header)
            <th><span class="text-green-600">{{$header}}</span></th>
        @endforeach
    </tr>
    </thead>
    @foreach(array_values($rows) as $row)
        <tr>
            <th>{{$loop->iteration}}.</th>
            @foreach($headers as $header)
                <td>{{$row[$header]??'---'}}</td>
            @endforeach
        </tr>
    @endforeach
</table>
