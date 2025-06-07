<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekening - {{ $table->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 4px;
            line-height: 1.1;
            margin: 0;
            margin-left: -52.5px;
            width: 8.5cm;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }

        .logo {
            width: 40px;
            height: 40px;
            margin: 0 10px 6px;
            display: block;
        }

        .restaurant-name {
            font-size: 8px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 2px;
        }

        .restaurant-subtitle {
            font-size: 9px;
            color: #666;
            margin-bottom: 6px;
        }

        .receipt-info {
            font-size: 9px;
            color: #666;
        }

        .receipt-info div {
            margin-bottom: 1px;
        }

        .round-section {
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
        }

        .round-header {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #374151;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
        }

        .round-time {
            font-size: 8px;
            color: #666;
            font-weight: normal;
        }

        .item-row {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3px;
            font-size: 9px;
            padding: 0 6px;
        }

        .item-info {
            flex: 1;
            padding-right: 4px;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 1px;
        }

        .item-details {
            font-size: 8px;
            color: #666;
        }

        .item-pricing {
            text-align: right;
            min-width: 50px;
        }

        .unit-price {
            font-size: 8px;
            color: #666;
        }

        .total-price {
            font-weight: bold;
        }

        .round-subtotal {
            text-align: right;
            font-weight: bold;
            margin-top: 4px;
            padding-top: 3px;
            border-top: 1px solid #eee;
            font-size: 10px;
        }

        .final-totals {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 2px solid #333;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
        }

        .grand-total {
            font-size: 12px;
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 3px;
            margin-top: 3px;
        }

        .no-orders {
            text-align: center;
            color: #666;
            margin: 20px 0;
            font-size: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .qr-section {
            margin-top: 16px;
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 12px;
        }

        .qr-image {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            display: block;
        }

        .qr-text {
            font-size: 8px;
            color: #333;
            margin-top: 8px;
            font-weight: bold;
        }

        .qr-subtext {
            font-size: 7px;
            color: #666;
            margin-top: 4px;
        }

        .qr-url {
            font-size: 8px;
            text-align: center;
            margin-top: 5px;
            color: #666;
        }

        /* Helper classes */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
<!-- Header -->
<div class="header">
    <img src="{{ public_path('images/dragon-small.png') }}" alt="De Gouden Draak Logo" class="logo">
    <div class="restaurant-name">De Gouden Draak</div>
    <div class="restaurant-subtitle">Chinese Restaurant</div>
    <div class="receipt-info">
        <div><strong>Rekening - {{ $table->name }}</strong></div>
        <div>{{ now()->format('d-m-Y H:i') }}</div>
        <div>Personen: {{ $table->people->count() }}</div>
    </div>
</div>

@if(count($rounds) > 0)
    @php
        $itemCount = 0;
        $itemsPerPage = 12; // Approximately 12 items per 10cm page
    @endphp

        <!-- Sales by Round -->
    @foreach($rounds as $roundNumber => $roundSales)
        @if($itemCount > 0 && $itemCount % $itemsPerPage == 0)
            <div class="page-break"></div>
            <!-- Repeat header on new page -->
            <div class="header">
                <img src="{{ public_path('images/dragon-small.png') }}" alt="De Gouden Draak Logo" class="logo">
                <div class="restaurant-name">De Gouden Draak</div>
                <div class="receipt-info">
                    <div><strong>Rekening - {{ $table->name }} (vervolg)</strong></div>
                </div>
            </div>
        @endif

        <div class="round-section">
            <div class="round-header">
                Ronde {{ $roundNumber }}
                @if(count($roundSales) > 0)
                    <span class="round-time">({{ $roundSales[0]->saleDate->format('H:i') }})</span>
                @endif
            </div>

            @foreach($roundSales as $sale)
                @php $itemCount++; @endphp
                <div class="item-row">
                    <div class="item-info">
                        <div class="item-name">{!! $sale->menuItem->naam !!}</div>
                        <div class="item-details">
                            Nr. {{ $sale->menuItem->menunummer }}
                            @if($sale->menuItem->menu_toevoeging)
                                - {{ $sale->menuItem->menu_toevoeging }}
                            @endif
                        </div>
                        <div class="item-details">
                            Aantal: {{ $sale->amount }}
                        </div>
                    </div>
                    <div class="item-pricing">
                        <div class="unit-price">€{{ number_format($sale->menuItem->price, 2, ',', '.') }} p/st</div>
                        <div class="total-price">
                            €{{ number_format($sale->menuItem->price * $sale->amount, 2, ',', '.') }}</div>
                    </div>
                </div>

                {{-- Add page break if approaching limit --}}
                @if($itemCount % $itemsPerPage == 0 && !$loop->last)
        </div>
        <div class="page-break"></div>
        <div class="header">
            <img src="{{ public_path('images/dragon-small.png') }}" alt="De Gouden Draak Logo" class="logo">
            <div class="restaurant-name">De Gouden Draak</div>
            <div class="receipt-info">
                <div><strong>Rekening - {{ $table->name }} (vervolg)</strong></div>
            </div>
        </div>
        <div class="round-section">
            <div class="round-header">
                Ronde {{ $roundNumber }} (vervolg)
            </div>
            @endif
            @endforeach

            <div class="round-subtotal">
                Subtotaal Ronde {{ $roundNumber }}:
                €{{ number_format(collect($roundSales)->sum(function($sale) { return $sale->menuItem->price * $sale->amount; }), 2, ',', '.') }}
            </div>
        </div>
    @endforeach

    <!-- Final Totals -->
    @php
        $total = $sales->sum(function($sale) { return $sale->menuItem->price * $sale->amount; });
        $priceExVat = ($total / 106) * 100;
        $vatAmount = $total - $priceExVat;

        // Generate unique review code based on table and date
        $reviewCode = base64_encode($table->id . '-' . now()->format('Ymd'));

        // Generate QR code URL for review form
        $reviewUrl = url('/review/' . $reviewCode);

        // Generate QR code using BaconQrCode
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(300),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCode = $writer->writeString($reviewUrl);
        $qrPath = public_path('qrcodes/review_'.$reviewCode.'.svg');
        if (!file_exists(dirname($qrPath))) {
            mkdir(dirname($qrPath), 0755, true);
        }
        file_put_contents($qrPath, $qrCode);
    @endphp

    {{-- Add page break if needed for totals --}}
    @if($itemCount % $itemsPerPage > 8)
        <div class="page-break"></div>
        <div class="header">
            <img src="{{ public_path('images/dragon-small.png') }}" alt="De Gouden Draak Logo" class="logo">
            <div class="restaurant-name">De Gouden Draak</div>
            <div class="receipt-info">
                <div><strong>Rekening - {{ $table->name }} (totaal)</strong></div>
            </div>
        </div>
    @endif

    <div class="final-totals">
        <div class="total-row">
            <span>Subtotaal (excl. BTW):</span>
            <span>€{{ number_format($priceExVat, 2, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>BTW (6%):</span>
            <span>€{{ number_format($vatAmount, 2, ',', '.') }}</span>
        </div>
        <div class="total-row grand-total">
            <span>Totaal:</span>
            <span>€{{ number_format($total, 2, ',', '.') }}</span>
        </div>
    </div>

    <!-- QR Code for Review Form -->
    <div class="qr-section">
        <img src="{{ $qrPath }}" alt="Review QR Code" class="qr-image">
        <div class="qr-url">{{ $reviewUrl }}</div>
        <div class="qr-text">Scan deze QR code om uw ervaring te delen!</div>
        <div class="qr-subtext">We waarderen uw mening en willen graag weten wat u van uw bezoek vond.</div>
        <div class="qr-subtext">Deel uw feedback en maak kans op 10% korting bij uw volgende bezoek!</div>
    </div>
@else
    <div class="no-orders">
        <p>Nog geen bestellingen geplaatst.</p>
    </div>
@endif

<div style="margin-top: 16px; text-align: center; font-size: 8px; color: #666;">
    <div>Bedankt voor uw bezoek!</div>
    <div>De Gouden Draak</div>
</div>
</body>
</html>
