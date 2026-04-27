<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .header { background: #4f46e5; color: white; padding: 20px; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f3f4f6; padding: 10px; text-align: left; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        .badge-low { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 9999px; font-size: 12px; }
        .badge-out { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 9999px; font-size: 12px; }
        .badge-expiry { background: #fde8d8; color: #9a3412; padding: 2px 8px; border-radius: 9999px; font-size: 12px; }
        .footer { color: #6b7280; font-size: 12px; padding: 15px 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0">⚠️ Alert Magazzino - {{ $company->name }}</h2>
        <p style="margin:5px 0 0">{{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <div class="content">
        <p>Si segnalano i seguenti alert attivi nel sistema WMS:</p>
        <table>
            <thead>
                <tr>
                    <th>Prodotto</th>
                    <th>Tipo Alert</th>
                    <th>Quantità Attuale</th>
                    <th>Soglia/Scadenza</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alerts as $alert)
                    <tr>
                        <td>{{ $alert->product->name }}<br><small style="color:#6b7280">{{ $alert->product->sku }}</small></td>
                        <td>
                            @if($alert->alert_type === 'out_of_stock')
                                <span class="badge-out">Esaurito</span>
                            @elseif($alert->alert_type === 'low_stock')
                                <span class="badge-low">Scorta Bassa</span>
                            @else
                                <span class="badge-expiry">In Scadenza</span>
                            @endif
                        </td>
                        <td>{{ $alert->current_quantity ?? '-' }}</td>
                        <td>
                            @if($alert->alert_type === 'expiry')
                                {{ $alert->expiry_date?->format('d/m/Y') }}
                            @else
                                Min: {{ $alert->threshold_quantity }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="footer">
        Questo messaggio è stato inviato automaticamente dal sistema WMS di {{ $company->name }}.
        Accedi al pannello per visualizzare tutti i dettagli e risolvere gli alert.
    </div>
</body>
</html>
