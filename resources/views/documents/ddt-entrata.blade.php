<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; }
    .header { display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #4f46e5; }
    .company-name { font-size: 18px; font-weight: bold; color: #4f46e5; }
    .doc-title { font-size: 16px; font-weight: bold; text-align: right; }
    .doc-number { font-size: 12px; color: #666; text-align: right; }
    .info-grid { display: flex; gap: 20px; margin-bottom: 15px; }
    .info-box { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 10px; }
    .info-box h4 { font-size: 9px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .info-box p { font-size: 10px; line-height: 1.5; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    thead th { background: #4f46e5; color: white; padding: 7px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; }
    tbody td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
    tbody tr:nth-child(even) td { background: #f9fafb; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .totals { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 4px; padding: 10px; margin-left: auto; width: 220px; }
    .totals table { margin: 0; }
    .totals td { border: none; padding: 3px 6px; }
    .totals .total-row td { font-weight: bold; font-size: 12px; color: #4f46e5; border-top: 1px solid #93c5fd; padding-top: 6px; }
    .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 9px; color: #9ca3af; }
    .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
    .badge-pending { background: #fef9c3; color: #854d0e; }
    .badge-partial { background: #dbeafe; color: #1e40af; }
    .badge-completed { background: #dcfce7; color: #166534; }
</style>
</head>
<body>
    <div class="header">
        <div>
            <div class="company-name">{{ $company->name }}</div>
            <p>{{ $company->address }}</p>
            <p>P.IVA: {{ $company->vat_number ?? 'N/D' }}</p>
            @if($company->email)<p>{{ $company->email }}</p>@endif
        </div>
        <div>
            <div class="doc-title">DOCUMENTO DI TRASPORTO</div>
            <div class="doc-number">N. {{ $order->order_number }}</div>
            <p style="text-align:right;color:#666;margin-top:4px;">Data: {{ $order->created_at->format('d/m/Y') }}</p>
            <p style="text-align:right;margin-top:4px;">
                <span class="badge badge-{{ $order->status }}">{{ strtoupper($order->status) }}</span>
            </p>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h4>Fornitore</h4>
            <p><strong>{{ $order->supplier->name }}</strong></p>
            @if($order->supplier->address)<p>{{ $order->supplier->address }}</p>@endif
            @if($order->supplier->contact_person)<p>Ref: {{ $order->supplier->contact_person }}</p>@endif
            @if($order->supplier->email)<p>{{ $order->supplier->email }}</p>@endif
        </div>
        <div class="info-box">
            <h4>Magazzino Destinazione</h4>
            <p><strong>{{ $company->name }}</strong></p>
            <p>{{ $company->address }}</p>
        </div>
        <div class="info-box">
            <h4>Dettagli Ordine</h4>
            <p>N. Ordine: <strong>{{ $order->order_number }}</strong></p>
            <p>Data: {{ $order->created_at->format('d/m/Y') }}</p>
            @if($order->expected_date)<p>Data Attesa: {{ $order->expected_date->format('d/m/Y') }}</p>@endif
            <p>Creato da: {{ $order->createdBy?->name ?? '—' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Prodotto</th>
                <th>SKU</th>
                <th class="text-center">Q.tà Ord.</th>
                <th class="text-center">Q.tà Ric.</th>
                <th class="text-right">Prezzo Unit.</th>
                <th class="text-right">Totale</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $item->product->name }}</strong></td>
                <td style="font-family:monospace;color:#6b7280;">{{ $item->product->sku ?? '—' }}</td>
                <td class="text-center">{{ $item->quantity_ordered }}</td>
                <td class="text-center">{{ $item->quantity_received ?? 0 }}</td>
                <td class="text-right">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td class="text-right">€ {{ number_format($item->quantity_ordered * $item->unit_price, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div>
            @if($order->notes)
            <div class="info-box" style="max-width:300px;">
                <h4>Note</h4>
                <p>{{ $order->notes }}</p>
            </div>
            @endif
        </div>
        <div class="totals">
            <table>
                <tr><td>Imponibile:</td><td class="text-right">€ {{ number_format($order->items->sum(fn($i) => $i->quantity_ordered * $i->unit_price), 2, ',', '.') }}</td></tr>
                <tr><td>IVA (22%):</td><td class="text-right">€ {{ number_format($order->items->sum(fn($i) => $i->quantity_ordered * $i->unit_price) * 0.22, 2, ',', '.') }}</td></tr>
                <tr class="total-row"><td>TOTALE:</td><td class="text-right">€ {{ number_format($order->items->sum(fn($i) => $i->quantity_ordered * $i->unit_price) * 1.22, 2, ',', '.') }}</td></tr>
            </table>
        </div>
    </div>

    <div class="footer">
        <span>Documento generato il {{ now()->format('d/m/Y \a\l\l\e H:i') }}</span>
        <span>{{ $company->name }} — Sistema WMS</span>
        <span>Pagina 1</span>
    </div>
</body>
</html>
