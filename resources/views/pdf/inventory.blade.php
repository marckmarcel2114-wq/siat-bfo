<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Inventario de Activos</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #004a99; padding-bottom: 10px; }
        .logo { font-size: 18px; font-weight: bold; color: #004a99; }
        .title { font-size: 14px; font-weight: bold; margin-top: 5px; text-transform: uppercase; }
        .date { font-size: 10px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 4px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; font-size: 8px; text-align: center; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
        
        .badge {
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        .text-mono { font-family: monospace; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">BANCO FORTALEZA S.A.</div>
        <div class="title">Reporte de Inventario de Activos</div>
        <div class="date">Fecha de Reporte: {{ $fecha }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%">Código</th>
                <th style="width: 18%">Marca / Modelo</th>
                <th style="width: 10%">Nro. Serie</th>
                <th style="width: 10%">Tipo Act.</th>
                <th style="width: 12%">Ciudad</th>
                <th style="width: 10%">Tipo Suc.</th>
                <th style="width: 12%">Sucursal</th>
                <th style="width: 10%">Estado</th>
                <th style="width: 8%">Specs</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $asset)
            <tr>
                <td class="text-bold">{{ $asset->codigo_activo }}</td>
                <td>
                    <div class="text-bold">{{ $asset->modelo->marca->nombre ?? '-' }}</div>
                    <div>{{ $asset->modelo->nombre ?? '-' }}</div>
                </td>
                <td class="text-mono">{{ $asset->numero_serie }}</td>
                <td>{{ $asset->tipoActivo->nombre }}</td>
                <td>{{ $asset->ubicacion->ciudad->nombre ?? '-' }}</td>
                <td>{{ $asset->ubicacion->tipo->nombre ?? '-' }}</td>
                <td>{{ $asset->ubicacion->nombre ?? '-' }}</td>
                <td>
                    <span class="badge">{{ $asset->estadoActivo->nombre }}</span>
                </td>
                <td>
                    @php
                        $processor = $asset->atributos->where('nombre', 'Procesador')->first()?->valor;
                        $gen = $asset->atributos->where('nombre', 'Generación')->first()?->valor;
                    @endphp
                    @if($processor) <div>{{ $processor }}</div> @endif
                    @if($gen) <div style="font-size: 8px; color: #666;">Gen: {{ $gen }}</div> @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Este documento es un reporte oficial de SIAT-BFO | Generado por: {{ Auth::user()->name }} | Página 1 de 1
    </div>
</body>
</html>
