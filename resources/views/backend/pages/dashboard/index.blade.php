@extends('backend.layouts.master')

@section('title', 'Owner Dashboard')

@section('admin-content')
    <style>
        .owner-dashboard {
            --dash-ink: #182230;
            --dash-muted: #778399;
            --dash-line: #e8edf3;
            --dash-blue: #4263eb;
            padding: 28px 24px 40px;
            color: var(--dash-ink);
        }

        .owner-dashboard .dash-heading {
            margin-bottom: 22px;
        }

        .owner-dashboard .dash-heading h2 {
            margin: 0 0 5px;
            font-size: 25px;
            font-weight: 700;
        }

        .owner-dashboard .dash-subtitle,
        .owner-dashboard .dash-muted {
            color: var(--dash-muted);
        }

        .owner-dashboard .dash-kpi {
            height: 100%;
            min-height: 150px;
            border: 1px solid var(--dash-line);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 5px 18px rgba(28, 39, 60, .045);
        }

        .owner-dashboard .dash-kpi-body {
            padding: 20px;
        }

        .owner-dashboard .dash-kpi-label {
            color: var(--dash-muted);
            font-size: 13px;
            font-weight: 600;
        }

        .owner-dashboard .dash-kpi-value {
            margin: 13px 0 4px;
            font-size: clamp(23px, 2vw, 31px);
            font-weight: 750;
            line-height: 1.15;
            overflow-wrap: anywhere;
        }

        .owner-dashboard .dash-kpi-note {
            color: var(--dash-muted);
            font-size: 12px;
        }

        .owner-dashboard .dash-kpi-icon {
            display: inline-flex;
            width: 38px;
            height: 38px;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            background: #eef2ff;
            color: var(--dash-blue);
        }

        .owner-dashboard .dash-section {
            margin-top: 24px;
            border: 1px solid var(--dash-line);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 5px 18px rgba(28, 39, 60, .045);
        }

        .owner-dashboard .dash-section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--dash-line);
        }

        .owner-dashboard .dash-section-heading h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .owner-dashboard .dash-section-content {
            padding: 18px 20px;
        }

        .owner-dashboard .dash-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .owner-dashboard .dash-table th {
            padding: 11px 12px;
            color: var(--dash-muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .045em;
            text-align: left;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .owner-dashboard .dash-table td {
            padding: 13px 12px;
            border-top: 1px solid #eef1f5;
            vertical-align: middle;
        }

        .owner-dashboard .dash-table .dash-number {
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .owner-dashboard .dash-alert-box {
            margin: 16px 20px 0;
            padding: 12px 14px;
            border: 1px solid #ffe2a8;
            border-radius: 10px;
            background: #fff9eb;
            color: #805500;
        }

        .owner-dashboard .dash-alert-box.is-clear {
            border-color: #bcebd1;
            background: #effbf4;
            color: #176a3a;
        }

        .owner-dashboard .dash-status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #f0f3f8;
            color: #526075;
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .owner-dashboard .dash-status.status-submitted { background: #fff4dc; color: #8c5c00; }
        .owner-dashboard .dash-status.status-prepared { background: #eaf2ff; color: #2857a5; }
        .owner-dashboard .dash-status.status-ready,
        .owner-dashboard .dash-status.status-delivered,
        .owner-dashboard .dash-status.status-sent { background: #e8f8ee; color: #176a3a; }

        .owner-dashboard .dash-details summary {
            color: var(--dash-blue);
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .owner-dashboard .dash-details ul {
            min-width: 190px;
            margin: 8px 0 0;
            padding-left: 18px;
            color: #526075;
            font-size: 12px;
        }

        .owner-dashboard .dash-empty {
            padding: 24px 12px !important;
            color: var(--dash-muted);
            text-align: center;
        }

        .owner-dashboard .dash-kpi-alert-icon {
            background: #fff0ed;
            color: #d9483b;
        }

        @media (max-width: 767.98px) {
            .owner-dashboard { padding: 20px 14px 30px; }
            .owner-dashboard .dash-section-heading,
            .owner-dashboard .dash-section-content { padding: 15px; }
            .owner-dashboard .dash-alert-box { margin: 14px 15px 0; }
            .owner-dashboard .dash-table th,
            .owner-dashboard .dash-table td { padding: 10px 9px; }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner owner-dashboard">
            <div id="dashboard-content">
                @include('backend.pages.dashboard.partials.overview')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const dashboardContent = document.getElementById('dashboard-content');
            const refreshUrl = @json(route('admin.dashboard'));
            let refreshInProgress = false;

            window.setInterval(async () => {
                if (refreshInProgress) return;

                refreshInProgress = true;
                try {
                    const response = await fetch(refreshUrl, {
                        cache: 'no-store',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Dashboard refresh failed (${response.status}).`);
                    }

                    const result = await response.json();
                    dashboardContent.innerHTML = result.html;
                    const refreshStatus = document.getElementById('dashboard-refresh-status');
                    if (refreshStatus) {
                        refreshStatus.textContent = `Updated at ${result.refreshed_at}`;
                        refreshStatus.classList.remove('text-danger');
                    }
                } catch (error) {
                    console.error(error);
                    const refreshStatus = document.getElementById('dashboard-refresh-status');
                    if (refreshStatus) {
                        refreshStatus.textContent = 'Live refresh failed; showing the last loaded data.';
                        refreshStatus.classList.add('text-danger');
                    }
                } finally {
                    refreshInProgress = false;
                }
            }, 30000);
        })();
    </script>
@endpush
