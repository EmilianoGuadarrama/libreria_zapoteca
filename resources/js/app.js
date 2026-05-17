import './bootstrap';

import jQuery from 'jquery';
window.$ = jQuery;
window.jQuery = jQuery;

import DataTable from 'datatables.net';

import 'datatables.net-bs5';

import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

window.DataTable = DataTable;

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

$(document).ready(function() {
    $('.mi-datatable').each(function() {
        var $table = $(this);
        var columnCount = $table.find('thead th').length;

        $table.DataTable({
            "language": {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 a 0 de 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ],
            // Guarda la página actual, la búsqueda y el orden en el navegador
            "stateSave": true,
            // Duración en segundos de la memoria (7200 = 2 horas)
            "stateDuration": 7200,
            // Invalida el estado guardado si cambió el número de columnas
            "stateLoadCallback": function(settings, callback) {
                var key = 'DataTables_' + settings.sInstance + '_' + window.location.pathname;
                try {
                    var data = JSON.parse(localStorage.getItem(key));
                    if (data && data.columns && data.columns.length !== columnCount) {
                        localStorage.removeItem(key);
                        return null;
                    }
                    return data;
                } catch(e) {
                    return null;
                }
            }
        });
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
