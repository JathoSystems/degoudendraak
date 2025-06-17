<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dagelijks Verkooprappport</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .summary-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #4F46E5;
        }
        .summary-card h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #666;
        }
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: #4F46E5;
            color: white;
            font-weight: bold;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🐉 De Gouden Draak</h1>
        <h2>Dagelijks Verkooprappport</h2>
        <p>{{ $report->report_date->format('d-m-Y') }}</p>
    </div>

    <div class="content">
        <h3>Verkoop Samenvatting</h3>
        
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Totale Omzet</h3>
                <div class="value">€{{ number_format($report->total_sales, 2, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <h3>Aantal Bestellingen</h3>
                <div class="value">{{ $report->total_orders }}</div>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <h3>Subtotaal (excl. BTW)</h3>
                <div class="value">€{{ number_format($priceExVat, 2, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <h3>BTW (6%)</h3>
                <div class="value">€{{ number_format($vatAmount, 2, ',', '.') }}</div>
            </div>
        </div>

        @if($report->total_orders > 0)
            <div class="summary-card" style="margin: 20px 0;">
                <h3>Gemiddelde Bestelling</h3>
                <div class="value">€{{ number_format($report->total_sales / $report->total_orders, 2, ',', '.') }}</div>
            </div>
        @endif

        <h3>Top 10 Verkochte Producten</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Categorie</th>
                    <th class="text-right">Aantal</th>
                    <th class="text-right">Omzet</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $topProducts = collect($report->sales_summary)
                        ->sortByDesc('total_revenue')
                        ->take(10);
                @endphp
                @forelse($topProducts as $item)
                    <tr>
                        <td>
                            @if($item['menu_number'])
                                <strong>{{ $item['menu_number'] }}{{ $item['menu_addition'] ?? '' }}.</strong>
                            @endif
                            {{ $item['menu_item_name'] }}
                        </td>
                        <td>{{ $item['category'] }}</td>
                        <td class="text-right">{{ $item['total_amount'] }}</td>
                        <td class="text-right">€{{ number_format($item['total_revenue'], 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #666;">Geen verkopen gevonden</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(count($report->sales_summary) > 10)
            <p style="text-align: center; color: #666; font-style: italic;">
                En {{ count($report->sales_summary) - 10 }} andere producten...
            </p>
        @endif

        <p style="margin-top: 30px;">
            <strong>📎 Bijlage:</strong> Het complete Excel rapport is toegevoegd als bijlage.<br>
            <strong>🌐 Online:</strong> Bekijk alle rapporten in het admin dashboard.
        </p>
    </div>

    <div class="footer">
        <p>Dit rapport is automatisch gegenereerd om {{ now()->format('d-m-Y H:i') }}</p>
        <p>De Gouden Draak Administratie Systeem</p>
    </div>
</body>
</html>
