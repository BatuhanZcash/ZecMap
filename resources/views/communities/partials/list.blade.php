@foreach ($communities as $index => $row)
    @include('communities.partials.card', [
        'row' => $row,
        'period' => $period,
        'rank' => $startRank + $index,
    ])
@endforeach
