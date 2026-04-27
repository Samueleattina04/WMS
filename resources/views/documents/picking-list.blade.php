<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; }
    .header { padding-bottom: 12px; border-bottom: 2px solid #059669; margin-bottom: 15px; }
    .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
    .company-name { font-size: 16px; font-weight: bold; color: #059669; }
    .doc-badge { background: #059669; color: white; padding: 4px 12px; border-radius: 4px; font-size: 14px; font-weight: bold; }
    .info-strip { display: flex; gap: 15px; margin-top: 10px; font-size: 10px; }
    .info-item { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 3px; padding: 5px 10px; }
    .info-item strong { display: block; font-size: 9px; color: #15803d; text-transform: uppercase; letter-spacing: 0.3px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    thead th { background: #059669; color: white; padding: 7px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; }
    tbody td { padding: 7px 8px; border-bottom: 1px solid #f3f4f6; }
    tbody tr:nth-child(even) td { background: #f9fafb; }
    .slot-badge { display: inline-block; background: #1e3a5f; color: white; font-family: monospace; font-size: 10px; padding: 2px 6px; border-radius: 3px; font-weight: bold; }
    .lot-badge { display: inline-block; background: #fef3c7; color: #92400e; font-size: 9px; padding: 2px 5px; border-radius: 3px; }
    .qty-big { font-size: 14px; font-weight: bold; text-align: center; }
    .check-box { width: 20px; height: 20px; border: 2px solid #374151; border-radius: 3px; display: inline-block; }
    .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 10px; display: flex; justify-content: space-between; font-size: 9px; color: #9ca3af; }
    .signature-area { margin-top: 30px; display: flex; gap: 30px; }
    .signature-box { flex: 1; border-top: 1px solid #374151; padding-top: 5px; font-size: 9px; color: #6b7280; text-align: center; }
    .fifo-badge { display: inline-block; background: #dbeafe; color: #1e40af; font-size: 8px; padding: 1px 4px; border-radius: 2px; }
</style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div>
                <div class="company-name">{{ $company->name }}</div>
                <p style="color:#6b7280;font-size:9px;">{{ $company->address }}</p>
            </div>
            <div class="doc-badge">LISTA DI PRELIEVO</div>
        </div>
        <div class="info-strip">
            <div class="info-item"><strong>N. Ordine</strong>{{ $order->order_number }}</div>
            <div class="info-item"><strong>Cliente</strong>{{ $order->customer->name }}</div>
            <div class="info-item"><strong>Data Emissione</strong>{{ now()->format('d/m/Y H:i') }}</div>
            @if($order->expected_date)
            <div class="info-item"><strong>Data Consegna</strong>{{ $order->expected_date->format('d/m/Y') }}</div>
            @endif
            <div class="info-item"><strong>Operatore</strong>_______________________</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:22px;"></th>
                <th>Prodotto</th>
                <th>SKU</th>
                <th>Posizione</th>
                <th>Lotto / Scadenza</th>
                <th style="text-align:center;">Q.tà Richiesta</th>
                <th style="text-align:center;">Q.tà Prelevata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pickingItems as $item)
            <tr>
                <td style="text-align:center;"><span class="check-box"></span></td>
                <td>
                    <strong>{{ $item['product_name'] }}</strong>
                    @if(isset($item['fifo']) && $item['fifo'])<span class="fifo-badge">FIFO</span>@endif
                </td>
                <td style="font-family:monospace;color:#6b7280;font-size:9px;">{{ $item['sku'] ?? '—' }}</td>
                <td>
                    @if(isset($item['slot_code']))
                        <span class="slot-badge">{{ $item['slot_code'] }}</span>
                    @else
                        <span style="color:#9ca3af;">Da assegnare</span>
                    @endif
                </td>
                <td>
                    @if(isset($item['lot_number']) && $item['lot_number'])
                        <span class="lot-badge">{{ $item['lot_number'] }}</span>
                    @endif
                    @if(isset($item['expiry_date']) && $item['expiry_date'])
                        <br><span style="font-size:9px;color:#6b7280;">Sc: {{ \Carbon\Carbon::parse($item['expiry_date'])->format('d/m/Y') }}</span>
                    @endif
                </td>
                <td class="qty-big">{{ $item['quantity'] }}</td>
                <td style="text-align:center;border-bottom: 1px dashed #374151;min-width:60px;">&nbsp;</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($order->notes)
    <div style="background:#fefce8;border:1px solid #fde68a;border-radius:4px;padding:8px 12px;margin-bottom:15px;">
        <strong style="font-size:9px;color:#92400e;">NOTE ORDINE:</strong>
        <p style="margin-top:2px;">{{ $order->notes }}</p>
    </div>
    @endif

    <div class="signature-area">
        <div class="signature-box">Prelevato da (firma)</div>
        <div class="signature-box">Controllato da (firma)</div>
        <div class="signature-box">Consegnato da (firma)</div>
    </div>

    <div class="footer">
        <span>Lista generata il {{ now()->format('d/m/Y \a\l\l\e H:i') }}</span>
        <span>Ordine: {{ $order->order_number }} — {{ $company->name }}</span>
        <span>Pag. 1</span>
    </div>
</body>
</html>
