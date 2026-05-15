<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>{{ $titulo }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #B22222;
            margin-bottom: 5px;
        }

        .fecha {
            text-align: center;
            margin-bottom: 20px;
        }

        .estadisticas {

            width: 100%;
            margin-bottom: 20px;
        }

        .estadisticas table {

            width: 100%;
            border-collapse: collapse;
        }

        .estadisticas td {

            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }

        .grafica {

            text-align: center;
            margin-bottom: 25px;
        }

        table {

            width: 100%;
            border-collapse: collapse;
        }

        table thead {

            background-color: #B22222;
            color: white;
        }

        table th,
        table td {

            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        table tbody tr:nth-child(even) {

            background-color: #f2f2f2;
        }
    </style>

</head>

<body>

    <h1>{{ $titulo }}</h1>

    <div class="fecha">

        Generado:
        {{ now()->format('d/m/Y H:i') }}

    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="estadisticas">

        <table>

            <tr>

                <td>
                    Total Ventas
                    <br>
                    ${{ number_format($totalVentas, 2) }}
                </td>

                <td>
                    Cantidad de Ventas
                    <br>
                    {{ $cantidadVentas }}
                </td>

                <td>
                    Promedio
                    <br>
                    ${{ number_format($promedioVentas, 2) }}
                </td>

            </tr>

        </table>

    </div>

    {{-- GRÁFICA --}}
    <div class="grafica">

        <img src="{{ $chartBase64 }}"
            width="500">

    </div>

    {{-- TABLA --}}
    <table>

        <thead>

            <tr>

                @foreach($columnas as $columna)

                <th>{{ $columna }}</th>

                @endforeach

            </tr>

        </thead>

        <tbody>

            @foreach($datos as $fila)

            <tr>

                @foreach($fila as $dato)

                <td>{{ $dato }}</td>

                @endforeach

            </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>