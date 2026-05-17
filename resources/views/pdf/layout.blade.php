<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ $titulo ?? 'Reporte' }}</title>

    <style>
        /* ── RESET Y BASE ───────────────────────────────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #2d2d2d;
            background: #ffffff;
        }

        /* ── CABECERA ───────────────────────────────────── */
        .pdf-header {
            background-color: #4b1c71;
            color: #ffffff;
            padding: 16px 22px;
            margin-bottom: 18px;
            border-radius: 4px;
        }

        .pdf-header-inner {
            width: 100%;
        }

        .pdf-header-logo-cell {
            width: 70px;
            vertical-align: middle;
        }

        .pdf-header-logo-cell img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 6px;
            background: #ffffff;
            padding: 3px;
        }

        .pdf-header-text-cell {
            vertical-align: middle;
            padding-left: 14px;
        }

        .pdf-header-text-cell h1 {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #ffffff;
            margin-bottom: 3px;
        }

        .pdf-header-text-cell p {
            font-size: 11px;
            color: #e0c9f5;
            margin: 0;
        }

        .pdf-header-date-cell {
            vertical-align: top;
            text-align: right;
            font-size: 10px;
            color: #dcc7f0;
            white-space: nowrap;
        }

        /* ── LÍNEA DECORATIVA ───────────────────────────── */
        .pdf-divider {
            height: 3px;
            background: linear-gradient(to right, #4b1c71, #9b59b6, #d7bde2);
            margin-bottom: 16px;
            border-radius: 2px;
        }

        /* ── SECCIÓN DE INFORMACIÓN GENERAL ────────────── */
        .pdf-info-box {
            background: #f8f4fc;
            border-left: 4px solid #4b1c71;
            padding: 10px 14px;
            margin-bottom: 16px;
            border-radius: 0 4px 4px 0;
            font-size: 10.5px;
        }

        .pdf-info-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .pdf-info-box td {
            padding: 3px 8px 3px 0;
            color: #444;
        }

        .pdf-info-box .info-label {
            font-weight: bold;
            color: #4b1c71;
            width: 130px;
        }

        /* ── ESTADÍSTICAS ───────────────────────────────── */
        .stats-title {
            font-size: 12px;
            font-weight: bold;
            color: #4b1c71;
            margin-bottom: 8px;
            border-bottom: 1px solid #d7bde2;
            padding-bottom: 4px;
        }

        .stats-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .stats-grid td {
            width: 20%;
            text-align: center;
            vertical-align: middle;
            padding: 10px 6px;
            border: 1px solid #e0d0f0;
            background: #f8f4fc;
        }

        .stats-grid .stat-value {
            font-size: 15px;
            font-weight: bold;
            color: #4b1c71;
            display: block;
        }

        .stats-grid .stat-label {
            font-size: 9px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-top: 3px;
        }

        /* ── GRÁFICA ────────────────────────────────────── */
        .pdf-chart-wrap {
            text-align: center;
            margin-bottom: 18px;
        }

        .pdf-chart-wrap img {
            max-width: 520px;
            height: auto;
            border: 1px solid #e0d0f0;
            border-radius: 4px;
            padding: 4px;
        }

        /* ── TABLA PRINCIPAL ────────────────────────────── */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #4b1c71;
            margin-bottom: 8px;
            border-bottom: 1px solid #d7bde2;
            padding-bottom: 4px;
        }

        .pdf-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10.5px;
        }

        .pdf-table thead tr {
            background-color: #4b1c71;
            color: #ffffff;
        }

        .pdf-table th {
            padding: 9px 8px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #3a1558;
        }

        .pdf-table td {
            padding: 7px 8px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        .pdf-table tbody tr:nth-child(even) {
            background-color: #f8f4fc;
        }

        .pdf-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* ── BADGES DE ESTADO ───────────────────────────── */
        .badge-procesado {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-pendiente {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-cancelada {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        /* ── TOTALES ────────────────────────────────────── */
        .pdf-totals {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .pdf-totals td {
            padding: 6px 10px;
            text-align: right;
            font-weight: bold;
            color: #4b1c71;
            font-size: 11px;
        }

        .pdf-totals .total-row td {
            background-color: #4b1c71;
            color: #ffffff;
            font-size: 12px;
        }

        /* ── PIE DE PÁGINA ──────────────────────────────── */
        .pdf-footer {
            margin-top: 24px;
            border-top: 2px solid #4b1c71;
            padding-top: 8px;
            text-align: center;
            font-size: 9px;
            color: #888;
        }

        .pdf-footer span {
            color: #4b1c71;
            font-weight: bold;
        }

        /* ── UTILIDAD ───────────────────────────────────── */
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .text-center { text-align: center; }
        .fw-bold     { font-weight: bold; }
        .text-purple { color: #4b1c71; }
        .mt-10       { margin-top: 10px; }
        .mb-10       { margin-bottom: 10px; }
    </style>
</head>

<body>

    {{-- ── CABECERA ──────────────────────────────────────── --}}
    <div class="pdf-header">
        <table class="pdf-header-inner">
            <tr>
                {{-- Logo --}}
                <td class="pdf-header-logo-cell">
                {{-- Logo: usa public_path() con barras normales (DomPDF en Windows) --}}
                @php $logoPath = str_replace('\\', '/', public_path('img/logo.png')); @endphp
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo Librería Zapotec">
                @else
                    <div style="width:60px;height:60px;background:#ffffff;border-radius:6px;display:inline-block;"></div>
                @endif
                </td>

                {{-- Nombre y módulo --}}
                <td class="pdf-header-text-cell">
                    <h1>Librería Zapotec</h1>
                    <p>{{ $titulo ?? 'Reporte' }}</p>
                </td>

                {{-- Fecha y usuario --}}
                <td class="pdf-header-date-cell">
                    <strong>Generado:</strong><br>
                    {{ now()->format('d/m/Y') }}<br>
                    {{ now()->format('H:i') }} hrs<br><br>
                    @if(isset($generadoPor))
                        <strong>Por:</strong><br>{{ $generadoPor }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ── LÍNEA DECORATIVA ──────────────────────────────── --}}
    <div class="pdf-divider"></div>

    {{-- ── CONTENIDO DEL MÓDULO ─────────────────────────── --}}
    @yield('contenido')

    {{-- ── PIE DE PÁGINA ─────────────────────────────────── --}}
    <div class="pdf-footer">
        Documento generado automáticamente por
        <span>Sistema Administrativo — Librería Zapotec</span>
        &nbsp;|&nbsp; {{ now()->format('d/m/Y H:i') }} hrs
    </div>

</body>

</html>