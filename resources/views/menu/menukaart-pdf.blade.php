<head>
    <title>
        {{ __("De Gouden Draak - Menukaart")}}
    </title>
</head>
<h1>
    {{ __("De Gouden Draak - Menukaart")}}
</h1>

@php
    $groepen = $menuItems->groupBy('soortgerecht');
@endphp

<div class="groepen-container">
    @foreach ($groepen as $soort => $gerechten)
        <div class="gerechtgroep">
            <h2>{{ ucfirst(strtolower($soort)) }}</h2>
            @foreach ($gerechten as $gerecht)
                <div class="gerecht">
                    <strong>{!! str_replace(['{', '}'], '', $gerecht->naam) !!}</strong><br>
                    @if (!empty($gerecht->beschrijving))
                        <em>{!! $gerecht->beschrijving !!}</em><br>
                    @endif
                    €{{ number_format($gerecht->price, 2, ',', '.') }}
                </div>
            @endforeach
        </div>
    @endforeach
</div>
