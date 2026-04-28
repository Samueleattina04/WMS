<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; }
    .company-name { font-size: 16px; font-weight: bold; color: #4f46e5; }
    .doc-title { font-size: 15px; font-weight: bold; text-align: right; }
    .info-label { font-size: 9px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 4px; }
    table.layout { width: 100%; border-collapse: collapse; }
    table.info { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    table.info td { vertical-align: top; padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; width: 33%; }
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    table.items thead th { background: #4f46e5; color: white; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
    table.items tbody td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; font-size: 10px; }
    table.items tbody tr:nth-child(even) td { background: #f9fafb; }
    table.totals { width: 220px; border-collapse: collapse; border: 1px solid #bae6fd; background: #f0f9ff; }
    table.totals td { padding: 4px 8px; font-size: 10px; }
    table.totals .total-row td { font-weight: bold; font-size: 12px; color: #4f46e5; border-top: 1px solid #93c5fd; padding-top: 6px; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
    .badge-pending { background: #fef9c3; color: #854d0e; }
    .badge-partial { background: #dbeafe; color: #1e40af; }
    .badge-completed { background: #dcfce7; color: #166534; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .separator { border-bottom: 2px solid #4f46e5; margin-bottom: 15px; padding-bottom: 12px; }
</style>
</head>
<body>

    {{-- Header --}}
    <div class="separator">
        <table class="layout">
            <tr>
                <td style="width:60%;vertical-align:top;">
                    <div class="company-name">{{ $company->name }}</div>
                    <div style="color:#555;margin-top:2px;">{{ $company->address }}</div>
                    <div style="color:#555;">P.IVA: {{ $company->vat_number ?? 'N/D' }}</div>
                    @if($company->email)<div style="color:#555;">{{ $company->email }}</div>@endif
                </td>
                <td style="width:40%;vertical-align:top;">
                    <div class="doc-title">DOCUMENTO DI TRASPORTO</div>
                    <div style="font-size:11px;color:#555;text-align:right;">N. {{ $order->order_number }}</div>
                    <div style="color:#666;margin-top:4px;font-size:9px;text-align:right;">Data: {{ $order->created_at->format('d/m/Y') }}</div>
                    <div style="margin-top:4px;text-align:right;">
                        <span class="badge badge-{{ $order->status }}">{{ strtoupper($order->status) }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Info boxes --}}
    <table class="info">
        <tr>
            <td>
                <div class="info-label">Fornitore</div>
                <strong>{{ $order->supplier->name }}</strong><br>
                @if($order->supplier->address){{ $order->supplier->address }}<br>@endif
                @if($order->supplier->contact_person)Ref: {{ $order->supplier->contact_person }}<br>@endif
                @if($order->supplier->email){{ $order->supplier->email }}@endif
            </td>
            <td>
                <div class="info-label">Magazzino Destinazione</div>
                <strong>{{ $company->name }}</strong><br>
                {{ $company->address }}
            </td>
            <td>
                <div class="info-label">Dettagli Ordine</div>
                N. Ordine: <strong>{{ $order->order_number }}</strong><br>
                Data: {{ $order->created_at->format('d/m/Y') }}<br>
                @if($order->expected_date)Data attesa: {{ $order->expected_date->format('d/m/Y') }}<br>@endif
                Creato da: {{ $order->createdBy?->name ?? '—' }}
            </td>
        </tr>
    </table>

    {{-- Items --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:25px;">#</th>
                <th>Prodotto</th>
                <th style="width:90px;">SKU</th>
                <th class="text-center" style="width:60px;">Q.tà Ord.</th>
                <th class="text-center" style="width:60px;">Q.tà Ric.</th>
                <th class="text-right" style="width:80px;">Prezzo Unit.</th>
                <th class="text-right" style="width:80px;">Totale</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $item->product->name }}</strong></td>
                <td style="font-family:monospace;color:#6b7280;font-size:9px;">{{ $item->product->sku ?? '—' }}</td>
                <td class="text-center">{{ $item->quantity_ordered }}</td>
                <td class="text-center">{{ $item->quantity_received ?? 0 }}</td>
                <td class="text-right">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td class="text-right">€ {{ number_format($item->quantity_ordered * $item->unit_price, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($order->notes)
    <div style="border:1px solid #e5e7eb;background:#f9fafb;padding:8px;margin-bottom:15px;font-size:10px;">
        <strong>Note:</strong> {{ $order->notes }}
    </div>
    @endif

    {{-- Totals aligned right via table --}}
    @php
        $imponibile = $order->items->sum(fn($i) => $i->quantity_ordered * $i->unit_price);
        $iva = $imponibile * 0.22;
        $totale = $imponibile + $iva;
    @endphp
    <table class="layout">
        <tr>
            <td style="width:55%;vertical-align:top;">&nbsp;</td>
            <td style="width:45%;vertical-align:top;">
                <table class="totals" style="width:100%;">
                    <tr><td>Imponibile:</td><td class="text-right">€ {{ number_format($imponibile, 2, ',', '.') }}</td></tr>
                    <tr><td>IVA (22%):</td><td class="text-right">€ {{ number_format($iva, 2, ',', '.') }}</td></tr>
                    <tr class="total-row"><td>TOTALE:</td><td class="text-right">€ {{ number_format($totale, 2, ',', '.') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <table class="layout" style="margin-top:25px;border-top:1px solid #e5e7eb;padding-top:8px;">
        <tr>
            <td style="font-size:9px;color:#9ca3af;">Documento generato il {{ now()->format('d/m/Y \a\l\l\e H:i') }}</td>
            <td style="font-size:9px;color:#9ca3af;text-align:center;">{{ $company->name }} — Sistema WMS</td>
            <td style="font-size:9px;color:#9ca3af;text-align:right;">Pagina 1</td>
        </tr>
    </table>

</body>
</html>
