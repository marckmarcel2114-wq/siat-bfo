<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Acta de Devolución de Activo</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .logo { font-size: 20px; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; margin-top: 10px; text-transform: uppercase; }
        .section { margin-bottom: 15px; }
        .section-title { font-weight: bold; background-color: #f0f0f0; padding: 5px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f9f9f9; }
        .signatures { margin-top: 50px; display: table; width: 100%; }
        .signature-box { display: table-cell; width: 50%; text-align: center; vertical-align: bottom; height: 80px; }
        .line { border-top: 1px solid #000; width: 80%; margin: 0 auto; margin-top: 50px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 10px; text-align: center; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">BANCO FORTALEZA S.A.</div>
        <div class="title">Acta de Devolución de Activo Fijo</div>
        <div>La Paz, {{ $fecha }}</div>
    </div>

    <div class="section">
        <div class="section-title">1. DATOS DEL FUNCIONARIO (QUIEN DEVUELVE)</div>
        <table>
            <tr>
                <th>Nombre Completo:</th>
                <td>{{ $usuario->name }} {{ $usuario->lastname }}</td>
            </tr>
            <tr>
                <th>Cargo:</th>
                <td>{{ $usuario->cargo ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">2. DETALLE DEL ACTIVO DEVUELTO</div>
        <table>
            <tr>
                <th>Código Activo:</th>
                <td><strong>{{ $activo->codigo_activo }}</strong></td>
                <th>Tipo:</th>
                <td>{{ $activo->tipoActivo->nombre }}</td>
            </tr>
            <tr>
                <th>Marca:</th>
                <td>{{ $activo->modelo->marca->nombre ?? 'N/A' }}</td>
                <th>Modelo:</th>
                <td>{{ $activo->modelo->nombre ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">3. ESTADO DE RECEPCIÓN</div>
        <p>
            El activo es devuelto al área de Sistemas/Activos Fijos.
        </p>
        <p>
            <strong>Observaciones de Devolución:</strong><br>
            {{ $observaciones ? $observaciones : "Sin observaciones adicionales." }}
        </p>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="line"></div>
            <strong>Devuelto por:</strong><br>
            {{ $usuario->name }} {{ $usuario->lastname }}<br>
            FUNCIONARIO
        </div>
        <div class="signature-box">
            <div class="line"></div>
            <strong>Recibido por:</strong><br>
            {{ Auth::user()->name }}<br>
            Admin. de Activos
        </div>
    </div>

    <div class="footer">
        Generado automáticamente por SIAT-BFO el {{ now() }} | ID Asignación (Cierre): {{ $asignacion->id }}
    </div>
</body>
</html>
