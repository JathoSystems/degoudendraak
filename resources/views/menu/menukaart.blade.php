<head>
    <title>
        De Gouden Draak - Menukaart
    </title>
</head>

<x-header/>
<x-layout1/>

<h1>Menukaart</h1>

<button><a href="/menukaart/download" class="url">Download als PDF</a></button>

@if($favorieteItems->isNotEmpty())
    <div class="gerechtgroep">
        <h2>Favorieten</h2>
        @foreach ($favorieteItems as $gerecht)
            <div class="gerecht">
                <strong>{!! str_replace(['{', '}'], '', $gerecht->naam) !!}</strong>
                <span class="favoriet-heart" data-id="{{ $gerecht->id }}" data-checked="true">♥</span>
                <br>
                @if (!empty($gerecht->beschrijving))
                    <em>{!! $gerecht->beschrijving !!}</em><br>
                @endif
                €{{ number_format($gerecht->price, 2, ',', '.') }}
            </div>
        @endforeach
    </div>
@endif

<div class="groepen-container">
    @foreach ($groepen as $soort => $gerechten)
        <div class="gerechtgroep">
            <h2>{{ ucfirst(strtolower($soort)) }}</h2>
            @foreach ($gerechten as $gerecht)
                <div class="gerecht">
                    <strong>{!! str_replace(['{', '}'], '', $gerecht->naam) !!}</strong>
                    <span class="favoriet-heart" data-id="{{ $gerecht->id }}" data-checked="{{ in_array($gerecht->id, $favorieten) ? 'true' : 'false' }}">
                        ♥
                    </span>
                    <br>
                    @if (!empty($gerecht->beschrijving))
                        <em>{!! $gerecht->beschrijving !!}</em><br>
                    @endif
                    €{{ number_format($gerecht->price, 2, ',', '.') }}
                </div>
            @endforeach
        </div>
    @endforeach
</div>

<x-layout2/>
