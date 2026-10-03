<link rel="shortcut icon" type="image/png"  href="{{ asset('admin/assets/images/icon/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet"  href="{{ asset('admin/assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet"  href="{{ asset('admin/assets/css/themify-icons.css') }}">
    <link rel="stylesheet"  href="{{ asset('admin/assets/css/metisMenu.css') }}">
    <link rel="stylesheet"  href="{{ asset('admin/assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet"  href="{{ asset('admin/assets/css/slicknav.min.css') }}">
    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <!-- others css -->
    <link rel="stylesheet"  href="{{ asset('admin/assets/css/typography.css') }}">
    <link rel="stylesheet"  href="{{ asset('admin/assets/css/default-css.css') }}">
    <link rel="stylesheet"  href="{{ asset('admin/assets/css/styles.css') }}">
    <link rel="stylesheet"  href="{{ asset('admin/assets/css/responsive.css') }}">
    <style>
        .table-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            width: 100%;
            margin-top: 16px;
            padding: 13px 15px;
            border: 1px solid #e8edf3;
            border-radius: 10px;
            background: #f9fbfd;
        }

        .table-pagination-summary {
            color: #778399;
            font-size: 13px;
        }

        .table-pagination-summary strong {
            color: #263448;
            font-weight: 700;
        }

        .table-pagination-controls {
            gap: 7px;
            margin: 0;
        }

        .table-pagination-controls .page-link {
            min-width: 94px;
            padding: 8px 12px;
            border: 1px solid #dce4ef;
            border-radius: 7px !important;
            background: #fff;
            color: #3659a2;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease;
        }

        .table-pagination-controls a.page-link:hover,
        .table-pagination-controls a.page-link:focus {
            border-color: #4263eb;
            background: #4263eb;
            color: #fff;
            text-decoration: none;
        }

        .table-pagination-controls .disabled .page-link {
            background: #f1f4f8;
            color: #a2acba;
            cursor: not-allowed;
        }

        @media (max-width: 575.98px) {
            .table-pagination {
                align-items: flex-start;
                flex-direction: column;
            }

            .table-pagination nav {
                align-self: flex-end;
            }
        }
    </style>
    <!-- modernizr css -->
    <script src="{{ asset('admin/assets/js/vendor/modernizr-2.8.3.min.js') }}"></script>
