<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #4b1c71;
        }

        .header p {
            margin: 5px 0;
            font-size: 11px;
        }

        .info table {
            width: 100%;
        }

        .info td {
            padding: 5px;
        }

        .label {
            font-weight: bold;
            width: 30%;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th {
            background-color: #4b1c71;
            color: #fff;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Librería Zapotec</h1>
        <p>{{ $titulo }}</p>
        <p>Fecha: {{ date('d/m/Y H:i') }}</p>
    </div>

    @yield('contenido')

    <div class="footer">
        Generado automáticamente por el sistema
    </div>

</body>

</html>