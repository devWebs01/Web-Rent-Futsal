@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <style>
        table.dataTable thead tr,
        table.dataTable thead th,
        table.dataTable tbody th,
        table.dataTable tbody td {
            text-align: center;
        }


        /* .pagination .page-item.active .page-link {
                background-color: #dc3545;
                border-color: #dc3545;
                color: #fff;
            }

            .pagination .page-item .page-link:hover {
                background-color: #e4606d;
                border-color: #e4606d;
                color: #fff;
            }

            .pagination .page-item .page-link {
                color: #dc3545;
                border: 1px solid #dc3545;
            }

            .pagination .page-item.disabled .page-link {
                color: #aaa;
                background-color: #f8f9fa;
            } */
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

    <script>
        $('.table').DataTable();
    </script>
@endpush