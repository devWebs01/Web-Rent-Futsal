@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <style>
        table.dataTable thead tr,
        table.dataTable thead th,
        table.dataTable tbody th,
        table.dataTable tbody td {
            text-align: center;
        }

        /* Pagination styling */
        .pagination .page-item.active .page-link {
            background-color: #dc3545; /* Warna merah */
            border-color: #dc3545; /* Warna merah */
            color: #fff; /* Warna teks putih */
        }

        .pagination .page-item .page-link:hover {
            background-color: #e4606d; /* Warna merah hover */
            border-color: #e4606d; /* Warna merah hover */
            color: #fff; /* Warna teks putih */
        }

        .pagination .page-item .page-link {
            color: #dc3545; /* Warna merah */
            border: 1px solid #dc3545; /* Border merah */
        }

        .pagination .page-item.disabled .page-link {
            color: #aaa; /* Warna abu-abu untuk disabled */
            background-color: #f8f9fa; /* Background abu-abu terang */
        }
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
