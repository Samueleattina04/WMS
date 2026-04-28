<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; }
    table.layout { width: 100%; border-collapse: collapse; }
    table.info-strip { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.info-strip td { padding: 5px 8px; border: 1px solid #bbf7d0; background: #f0fdf4; font-size: 10px; }
    .info-label { font-size: 9px; font-weight: bold; color: #15803d; text-transform: uppercase; display: block; }
    .company-name { font-size: 15px; font-weight: bold; color: #059669; }
    .doc-badge { background: #059669; color: white; padding: 4px 12px; font-size: 13px; font-weight: bold; }
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 15px; margin-top: 15px; }
    table.items thead th { background: #059669; color: white; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
    table.items tbody td { padding: 7px 8px; border-bottom: 1px solid #f0f0f0; }
    table.items tbody tr:nth-child(even) td { background: #f9fafb; }
    .slot-badge { background: #1e3a5f; color: white; font-family: monospace; font-size: 10px; padding: 2px 6px; font-weight: bold; }
    .lot-badge { background: #fef3c7; color: #92400e; font-size: 9px; padding: 2px 5px; }
    .fifo-badge { background: #dbeafe; color: #1e40af; font-size: 8px; padding: 1px 4px; }
    .check-box { width: 18px; height: 18px; border: 2px solid #374151; display: inline-block; }
    .qty-big { font-size: 13px; font-weight: bold; text-align: center; }
    table.signatures { width: 100%; border-collapse: collapse; margin-top: 30px; }
    table.signatures td { border-top: 1px solid #374151; padding-top: 5px; font-size: 9px; color: #6b7280; text-align: center; width: 33%; padding-left: 10px; padding-right: 10px; }
</style>
</head>
<body>

    {{-- Header --}}
    <div style="border-bottom: 2px solid #059669; margin-bottom: 12px; padding-bottom: 10px;">
        <table class="layout">
            <tr>
                <td style="vertical-align:top;">
                    <div class="company-name">{{ $company->name }}</div>
                    <div style="color:#6b7280;font-size:9px;">{{ $company->address }}</div>
                </td>
                <td style="vertical-align:top;text-align:right;">
                    <span class="doc-badge">LISTA DI PRELIEVO</span>
                </td>
            </tr>
        </table>

        <table class="info-strip" style="margin-top:10px;">
            <tr>
                <td><span class="info-label">N. Ordine</span>{{ $order->order_number }}</td>
                <td><span class="info-label">Cliente</span>{{ $order->customer->name }}</td>
                <td><span class="info-label">Data Emissione</span>{{ now()->format('d/m/Y H:i') }}</td>
                @if($order->expected_date)
                <td><span class="info-label">Data Consegna</span>{{ $order->expected_date->format('d/m/Y') }}</td>
                @endif
                <td><span class="info-label">Operatore</span>_______________________</td>
            </tr>
        </table>
    </div>

    {{-- Items --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:22px;"></th>
                <th>Prodotto</th>
                <th style="width:80px;">SKU</th>
                <th style="width:80px;">Posizione</th>
                <th style="width:100px;">Lotto / Scadenza</th>
                <th class="text-center" style="width:65px;">Q.tà Rich.</th>
                <th class="text-center" style="width:65px;">Q.tà Prel.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pickingItems as $item)
            <tr>
                <td style="text-align:center;"><span class="check-box"></span></td>
                <td>
                    <strong>{{ $item['product_name'] }}</strong>
                    @if(!empty($item['fifo'])) <span class="fifo-badge">FIFO</span>@endif
                </td>
                <td style="font-family:monospace;color:#6b7280;font-size:9px;">{{ $item['sku'] ?? '—' }}</td>
                <td>
                    @if(!empty($item['slot_code']))
                        <span class="slot-badge">{{ $item['slot_code'] }}</span>
                    @else
                        <span style="color:#9ca3af;">Da assegnare</span>
                    @endif
                </td>
                <td>
                    @if(!empty($item['lot_number']))
                        <span class="lot-badge">{{ $item['lot_number'] }}</span><br>
                    @endif
                    @if(!empty($item['expiry_date']))
                        <span style="font-size:9px;color:#6b7280;">Sc: {{ \Carbon\Carbon::parse($item['expiry_date'])->format('d/m/Y') }}</span>
                    @endif
                    @if(empty($item['lot_number']) && empty($item['expiry_date']))—@endif
                </td>
                <td class="qty-big">{{ $item['quantity'] }}</td>
                <td style="text-align:center;border-bottom:1px dashed #374151;">&nbsp;</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($order->notes)
    <div style="background:#fefce8;border:1px solid #fde68a;padding:8px;margin-bottom:15px;font-size:10px;">
        <strong>Note ordine:</strong> {{ $order->notes }}
    </div>
    @endif

    {{-- Signatures --}}
    <table class="signatures">
        <tr>
            <td>Prelevato da (firma)</td>
            <td>Controllato da (firma)</td>
            <td>Consegnato da (firma)</td>
        </tr>
    </table>

    {{-- Footer --}}
    <table class="layout" style="margin-top:15px;border-top:1px solid #e5e7eb;padding-top:8px;">
        <tr>
            <td style="font-size:9px;color:#9ca3af;">Lista generata il {{ now()->format('d/m/Y \a\l\l\e H:i') }}</td>
            <td style="font-size:9px;color:#9ca3af;text-align:center;">Ordine: {{ $order->order_number }} — {{ $company->name }}</td>
            <td style="font-size:9px;color:#9ca3af;text-align:right;">Pag. 1</td>
        </tr>
    </table>

</body>
</html>
