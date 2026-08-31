<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">


<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Activity Logs</title>

{{-- Bootstrap --}}
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

{{-- Bootstrap Icons --}}
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet"
>

<style>

    /* =====================================================
       GLOBAL
    ====================================================== */

    :root {
        --primary: #4f46e5;
        --primary-dark: #4338ca;
        --success: #16a34a;
        --danger: #dc2626;
        --warning: #d97706;
        --info: #0284c7;
        --dark: #0f172a;
        --muted: #64748b;
        --border: #e2e8f0;
        --background: #f8fafc;
    }

    body {
        margin: 0;
        background: var(--background);
        color: var(--dark);
        font-family:
            Inter,
            -apple-system,
            BlinkMacSystemFont,
            "Segoe UI",
            sans-serif;
    }

    /* =====================================================
       PAGE
    ====================================================== */

    .activity-page {
        min-height: 100vh;
        padding: 32px;
    }

    .page-container {
        max-width: 1600px;
        margin: auto;
    }

    /* =====================================================
       HEADER
    ====================================================== */

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 28px;
    }

    .page-title-wrapper {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .page-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(
            135deg,
            #4f46e5,
            #7c3aed
        );
        color: #fff;
        font-size: 23px;
        box-shadow:
            0 10px 25px rgba(79, 70, 229, .20);
    }

    .page-title {
        margin: 0;
        font-size: 28px;
        font-weight: 750;
        letter-spacing: -.5px;
    }

    .page-subtitle {
        margin: 4px 0 0;
        color: var(--muted);
        font-size: 14px;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .modern-btn {
        border-radius: 10px;
        padding: 10px 16px;
        font-weight: 600;
        font-size: 14px;
        transition: all .2s ease;
    }

    .modern-btn:hover {
        transform: translateY(-1px);
    }

    /* =====================================================
       STATISTICS
    ====================================================== */

    .stats-grid {
        display: grid;
        grid-template-columns:
            repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow:
            0 4px 15px rgba(15, 23, 42, .04);
        transition: all .2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow:
            0 10px 25px rgba(15, 23, 42, .08);
    }

    .stat-label {
        color: var(--muted);
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .stat-number {
        font-size: 27px;
        font-weight: 750;
        line-height: 1;
    }

    .stat-description {
        color: #94a3b8;
        font-size: 12px;
        margin-top: 6px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-purple {
        background: #eef2ff;
        color: #4f46e5;
    }

    .stat-green {
        background: #ecfdf5;
        color: #16a34a;
    }

    .stat-orange {
        background: #fff7ed;
        color: #ea580c;
    }

    .stat-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    /* =====================================================
       CARD
    ====================================================== */

    .modern-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow:
            0 4px 15px rgba(15, 23, 42, .04);
    }

    /* =====================================================
       FILTER
    ====================================================== */

    .filter-card {
        padding: 22px;
        margin-bottom: 22px;
    }

    .filter-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .filter-heading h6 {
        margin: 0;
        font-weight: 700;
        font-size: 15px;
    }

    .filter-heading span {
        color: var(--muted);
        font-size: 12px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 7px;
    }

    .form-control,
    .form-select {
        min-height: 43px;
        border-radius: 10px;
        border: 1px solid #dbe3ec;
        font-size: 13px;
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #818cf8;
        box-shadow:
            0 0 0 3px rgba(99, 102, 241, .10) !important;
    }

    .search-wrapper {
        position: relative;
    }

    .search-wrapper i {
        position: absolute;
        left: 14px;
        top: 13px;
        color: #94a3b8;
    }

    .search-wrapper input {
        padding-left: 40px;
    }

    /* =====================================================
       ACTION BAR
    ====================================================== */

    .action-bar {
        padding: 17px 20px;
        margin-bottom: 18px;
    }

    .result-count {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .result-count strong {
        font-size: 19px;
    }

    .result-count span {
        color: var(--muted);
        font-size: 13px;
    }

    /* =====================================================
       TABLE
    ====================================================== */

    .table-card {
        overflow: hidden;
    }

    .table-responsive {
        max-height: 650px;
        overflow: auto;
    }

    .modern-table {
        margin: 0;
        min-width: 1100px;
    }

    .modern-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
        color: #64748b;
        font-size: 11px;
        font-weight: 750;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 15px 16px;
        white-space: nowrap;
    }

    .modern-table tbody td {
        padding: 15px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 13px;
    }

    .modern-table tbody tr {
        transition: background .15s ease;
    }

    .modern-table tbody tr:hover {
        background: #fafbff;
    }

    .log-id {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 30px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
    }

    /* =====================================================
       USER
    ====================================================== */

    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 13px;
        font-weight: 750;
    }

    .user-name {
        font-weight: 700;
        color: #1e293b;
    }

    .user-id {
        color: #94a3b8;
        font-size: 11px;
        margin-top: 2px;
    }

    /* =====================================================
       ACTION BADGES
    ====================================================== */

    .activity-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 750;
        white-space: nowrap;
    }

    .badge-created {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-updated {
        background: #fef3c7;
        color: #a16207;
    }

    .badge-deleted,
    .badge-force-deleted {
        background: #fee2e2;
        color: #b91c1c;
    }

    .badge-viewed {
        background: #e0f2fe;
        color: #0369a1;
    }

    .badge-exported {
        background: #ede9fe;
        color: #6d28d9;
    }

    .badge-imported {
        background: #fce7f3;
        color: #be185d;
    }

    .badge-duplicated {
        background: #e0e7ff;
        color: #4338ca;
    }

    .badge-default {
        background: #f1f5f9;
        color: #475569;
    }

    /* =====================================================
       MODEL
    ====================================================== */

    .model-name {
        font-weight: 700;
        color: #334155;
    }

    .model-id {
        display: inline-block;
        margin-top: 3px;
        padding: 3px 7px;
        border-radius: 6px;
        background: #f8fafc;
        color: #64748b;
        font-size: 10px;
        font-weight: 700;
    }

    /* =====================================================
       DESCRIPTION
    ====================================================== */

    .description-cell {
        max-width: 280px;
    }

    .description-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #475569;
    }

    /* =====================================================
       IP
    ====================================================== */

    .ip-address {
        display: inline-block;
        padding: 5px 8px;
        background: #f8fafc;
        border-radius: 7px;
        color: #475569;
        font-size: 11px;
        font-family: monospace;
    }

    /* =====================================================
       TIME
    ====================================================== */

    .date-text {
        font-weight: 700;
        color: #334155;
    }

    .relative-time {
        color: #94a3b8;
        font-size: 11px;
        margin-top: 3px;
    }

    /* =====================================================
       BUTTONS
    ====================================================== */

    .table-action {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
    }

    .view-btn {
        background: #e0f2fe;
        color: #0284c7;
    }

    .delete-btn {
        background: #fee2e2;
        color: #dc2626;
    }

    .table-action:hover {
        transform: translateY(-1px);
    }

    /* =====================================================
       PAGINATION
    ====================================================== */

    .pagination-wrapper {
        padding: 18px 20px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: center;
    }

    .pagination {
        margin: 0;
        gap: 4px;
    }

    .pagination .page-link {
        border: none;
        border-radius: 8px;
        min-width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .pagination .page-link:hover {
        background: #eef2ff;
        color: #4f46e5;
    }

    .pagination .active .page-link {
        background: #4f46e5;
        color: #fff;
    }

    /* =====================================================
       EMPTY STATE
    ====================================================== */

    .empty-state {
        padding: 75px 20px !important;
        text-align: center;
    }

    .empty-icon {
        width: 70px;
        height: 70px;
        margin: auto;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #94a3b8;
        font-size: 30px;
    }

    .empty-state h5 {
        margin-top: 18px;
        font-weight: 750;
    }

    .empty-state p {
        color: #94a3b8;
        font-size: 13px;
    }

    /* =====================================================
       MODAL
    ====================================================== */

    .modal-content {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        box-shadow:
            0 25px 70px rgba(15, 23, 42, .20);
    }

    .modal-header {
        padding: 20px 22px;
        border-bottom: 1px solid var(--border);
    }

    .modal-title {
        font-weight: 750;
    }

    .detail-card {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px;
        height: 100%;
        background: #fafbfc;
    }

    .detail-label {
        font-size: 10px;
        font-weight: 750;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: .5px;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 13px;
        font-weight: 650;
        color: #334155;
        word-break: break-word;
    }

    .values-box {
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        font-size: 12px;
        max-height: 280px;
        overflow: auto;
    }

    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 1100px) {

        .stats-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

    }

    @media (max-width: 768px) {

        .activity-page {
            padding: 18px;
        }

        .page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .page-title {
            font-size: 23px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .header-actions {
            width: 100%;
        }

        .header-actions .btn {
            flex: 1;
        }

        .filter-card {
            padding: 16px;
        }

    }

    @media (max-width: 576px) {

        .page-icon {
            width: 44px;
            height: 44px;
            font-size: 19px;
        }

        .page-subtitle {
            font-size: 12px;
        }

        .modern-btn {
            padding: 9px 12px;
        }

    }

</style>


</head>

<body>

<div class="activity-page">

<div class="page-container">


{{-- =====================================================
     HEADER
====================================================== --}}

<div class="page-header">

    <div class="page-title-wrapper">

        <div class="page-icon">
            <i class="bi bi-activity"></i>
        </div>

        <div>

            <h1 class="page-title">
                Activity Logs
            </h1>

            <p class="page-subtitle">
                Monitor, search and manage application activities
            </p>

        </div>

    </div>


    <div class="header-actions">

        <a
            href="{{ route('dashboard') }}"
            class="btn btn-outline-secondary modern-btn"
        >
            <i class="bi bi-speedometer2 me-1"></i>
            Dashboard
        </a>

        <a
            href="{{ route('items.index') }}"
            class="btn btn-dark modern-btn"
        >
            <i class="bi bi-box me-1"></i>
            Items
        </a>

    </div>

</div>


{{-- =====================================================
     STATISTICS
====================================================== --}}

<div class="stats-grid">

    {{-- TOTAL --}}

    <div class="stat-card">

        <div>

            <div class="stat-label">
                TOTAL ACTIVITIES
            </div>

            <div class="stat-number">
                {{ number_format($totalLogs) }}
            </div>

            <div class="stat-description">
                All recorded activities
            </div>

        </div>

        <div class="stat-icon stat-purple">
            <i class="bi bi-activity"></i>
        </div>

    </div>


    {{-- TODAY --}}

    <div class="stat-card">

        <div>

            <div class="stat-label">
                TODAY
            </div>

            <div class="stat-number">
                {{ number_format($todayLogs) }}
            </div>

            <div class="stat-description">
                Activities today
            </div>

        </div>

        <div class="stat-icon stat-green">
            <i class="bi bi-calendar-check"></i>
        </div>

    </div>


    {{-- WEEK --}}

    <div class="stat-card">

        <div>

            <div class="stat-label">
                THIS WEEK
            </div>

            <div class="stat-number">
                {{ number_format($thisWeekLogs) }}
            </div>

            <div class="stat-description">
                Current week activities
            </div>

        </div>

        <div class="stat-icon stat-orange">
            <i class="bi bi-calendar-week"></i>
        </div>

    </div>


    {{-- MONTH --}}

    <div class="stat-card">

        <div>

            <div class="stat-label">
                THIS MONTH
            </div>

            <div class="stat-number">
                {{ number_format($thisMonthLogs) }}
            </div>

            <div class="stat-description">
                Current month activities
            </div>

        </div>

        <div class="stat-icon stat-blue">
            <i class="bi bi-calendar-month"></i>
        </div>

    </div>

</div>


{{-- =====================================================
     FILTER CARD
====================================================== --}}

<div class="modern-card filter-card">

    <div class="filter-heading">

        <div>

            <h6>
                <i class="bi bi-funnel me-2 text-primary"></i>
                Filters & Search
            </h6>

        </div>

        <span>
            Refine your activity logs
        </span>

    </div>


    <form
        method="GET"
        action="{{ route('activity-logs.index') }}"
    >

        <div class="row g-3">

            {{-- SEARCH --}}

            <div class="col-xl-4 col-lg-6">

                <label class="form-label">
                    Search
                </label>

                <div class="search-wrapper">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Search action, description or IP..."
                    >

                </div>

            </div>


            {{-- USER ID --}}

            <div class="col-xl-2 col-lg-3 col-md-6">

                <label class="form-label">
                    User ID
                </label>

                <input
                    type="number"
                    name="user_id"
                    class="form-control"
                    value="{{ request('user_id') }}"
                    placeholder="User ID"
                    min="1"
                >

            </div>


            {{-- ACTION --}}

            <div class="col-xl-2 col-lg-3 col-md-6">

                <label class="form-label">
                    Action
                </label>

                <select
                    name="action"
                    class="form-select"
                >

                    <option value="">
                        All Actions
                    </option>

                    @foreach($actions as $action)

                        <option
                            value="{{ $action }}"
                            {{ request('action') === $action ? 'selected' : '' }}
                        >
                            {{ ucfirst(str_replace('_', ' ', $action)) }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- DATE FROM --}}

            <div class="col-xl-2 col-lg-3 col-md-6">

                <label class="form-label">
                    Date From
                </label>

                <input
                    type="date"
                    name="date_from"
                    class="form-control"
                    value="{{ request('date_from') }}"
                >

            </div>


            {{-- DATE TO --}}

            <div class="col-xl-2 col-lg-3 col-md-6">

                <label class="form-label">
                    Date To
                </label>

                <input
                    type="date"
                    name="date_to"
                    class="form-control"
                    value="{{ request('date_to') }}"
                >

            </div>


            {{-- SORT BY --}}

            <div class="col-lg-3 col-md-6">

                <label class="form-label">
                    Sort By
                </label>

                <select
                    name="sort_by"
                    class="form-select"
                >

                    <option
                        value="created_at"
                        {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}
                    >
                        Date
                    </option>

                    <option
                        value="action"
                        {{ request('sort_by') === 'action' ? 'selected' : '' }}
                    >
                        Action
                    </option>

                    <option
                        value="ip_address"
                        {{ request('sort_by') === 'ip_address' ? 'selected' : '' }}
                    >
                        IP Address
                    </option>

                    <option
                        value="id"
                        {{ request('sort_by') === 'id' ? 'selected' : '' }}
                    >
                        ID
                    </option>

                </select>

            </div>


            {{-- SORT ORDER --}}

            <div class="col-lg-3 col-md-6">

                <label class="form-label">
                    Sort Order
                </label>

                <select
                    name="sort_order"
                    class="form-select"
                >

                    <option
                        value="desc"
                        {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}
                    >
                        Descending
                    </option>

                    <option
                        value="asc"
                        {{ request('sort_order') === 'asc' ? 'selected' : '' }}
                    >
                        Ascending
                    </option>

                </select>

            </div>


            {{-- APPLY --}}

            <div class="col-lg-3 col-md-6 d-flex align-items-end">

                <button
                    type="submit"
                    class="btn btn-primary modern-btn w-100"
                >

                    <i class="bi bi-funnel me-1"></i>

                    Apply Filters

                </button>

            </div>


            {{-- RESET --}}

            <div class="col-lg-3 col-md-6 d-flex align-items-end">

                <a
                    href="{{ route('activity-logs.index') }}"
                    class="btn btn-outline-secondary modern-btn w-100"
                >

                    <i class="bi bi-arrow-counterclockwise me-1"></i>

                    Reset Filters

                </a>

            </div>

        </div>

    </form>

</div>


{{-- =====================================================
     ACTION BAR
====================================================== --}}

<div class="modern-card action-bar">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div class="result-count">

            <strong>
                {{ number_format($logs->total()) }}
            </strong>

            <span>
                activity logs found
            </span>

        </div>


        <div class="d-flex gap-2 flex-wrap">

            {{-- EXPORT --}}

            <a
                href="{{ route('activity-logs.export', request()->query()) }}"
                class="btn btn-success modern-btn"
            >

                <i class="bi bi-file-earmark-spreadsheet me-1"></i>

                Export CSV

            </a>


            {{-- CLEAR OLD --}}

            <form
                action="{{ route('activity-logs.clear-old') }}"
                method="POST"
                class="d-inline"
                onsubmit="return confirm('Are you sure you want to clear logs older than 30 days?')"
            >

                @csrf

                <input
                    type="hidden"
                    name="days"
                    value="30"
                >

                <button
                    type="submit"
                    class="btn btn-outline-danger modern-btn"
                >

                    <i class="bi bi-trash3 me-1"></i>

                    Clear Old Logs

                </button>

            </form>

        </div>

    </div>

</div>


{{-- =====================================================
     TABLE
====================================================== --}}

<div class="modern-card table-card">

    <div class="table-responsive">

        <table class="table modern-table align-middle">

            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        User
                    </th>

                    <th>
                        Action
                    </th>

                    <th>
                        Model
                    </th>

                    <th>
                        Description
                    </th>

                    <th>
                        IP Address
                    </th>

                    <th>
                        Time
                    </th>

                    <th class="text-center">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

            @forelse($logs as $log)

                <tr>

                    {{-- ID --}}

                    <td>

                        <span class="log-id">
                            #{{ $log->id }}
                        </span>

                    </td>


                    {{-- USER --}}

                    <td>

                        <div class="user-cell">

                            <div class="user-avatar">

                                @if($log->user)

                                    {{ strtoupper(substr($log->user->name, 0, 1)) }}

                                @else

                                    <i class="bi bi-cpu"></i>

                                @endif

                            </div>


                            <div>

                                @if($log->user)

                                    <div class="user-name">
                                        {{ $log->user->name }}
                                    </div>

                                    <div class="user-id">
                                        User ID: {{ $log->user_id }}
                                    </div>

                                @else

                                    <div class="user-name">
                                        System
                                    </div>

                                    <div class="user-id">
                                        Automated Activity
                                    </div>

                                @endif

                            </div>

                        </div>

                    </td>


                    {{-- ACTION --}}

                    <td>

                        @php

                            $action = strtolower($log->action ?? '');

                            $badgeClass = match($action) {

                                'created'
                                    => 'badge-created',

                                'updated'
                                    => 'badge-updated',

                                'deleted'
                                    => 'badge-deleted',

                                'force_deleted'
                                    => 'badge-force-deleted',

                                'viewed'
                                    => 'badge-viewed',

                                'exported'
                                    => 'badge-exported',

                                'imported'
                                    => 'badge-imported',

                                'duplicated'
                                    => 'badge-duplicated',

                                default
                                    => 'badge-default',

                            };

                            $actionIcon = match($action) {

                                'created'
                                    => 'bi-plus-circle',

                                'updated'
                                    => 'bi-pencil',

                                'deleted',
                                'force_deleted'
                                    => 'bi-trash',

                                'viewed'
                                    => 'bi-eye',

                                'exported'
                                    => 'bi-download',

                                'imported'
                                    => 'bi-upload',

                                'duplicated'
                                    => 'bi-copy',

                                default
                                    => 'bi-activity',

                            };

                        @endphp


                        <span
                            class="activity-badge {{ $badgeClass }}"
                        >

                            <i class="bi {{ $actionIcon }}"></i>

                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}

                        </span>

                    </td>


                    {{-- MODEL --}}

                    <td>

                        @if($log->model_type)

                            <div class="model-name">

                                {{ class_basename($log->model_type) }}

                            </div>

                            <span class="model-id">

                                #{{ $log->model_id ?? 'N/A' }}

                            </span>

                        @else

                            <span class="text-muted">
                                N/A
                            </span>

                        @endif

                    </td>


                    {{-- DESCRIPTION --}}

                    <td class="description-cell">

                        <div
                            class="description-text"
                            title="{{ $log->description }}"
                        >

                            {{ $log->description ?: '-' }}

                        </div>

                    </td>


                    {{-- IP --}}

                    <td>

                        <span class="ip-address">

                            <i class="bi bi-globe2 me-1"></i>

                            {{ $log->ip_address ?: 'N/A' }}

                        </span>

                    </td>


                    {{-- TIME --}}

                    <td>

                        <div class="date-text">

                            {{ optional($log->created_at)->format('d M Y') }}

                        </div>

                        <div class="relative-time">

                            {{ optional($log->created_at)->diffForHumans() }}

                        </div>

                    </td>


                    {{-- ACTIONS --}}

                    <td class="text-center">

                        <div class="d-flex justify-content-center gap-2">

                            {{-- VIEW --}}

                            <button
                                type="button"
                                class="table-action view-btn"
                                onclick="viewLog({{ $log->id }})"
                                title="View Details"
                            >

                                <i class="bi bi-eye"></i>

                            </button>


                            {{-- DELETE --}}

                            <form
                                action="{{ route('activity-logs.destroy', $log->id) }}"
                                method="POST"
                                onsubmit="return confirm('Delete this activity log?')"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="table-action delete-btn"
                                    title="Delete Log"
                                >

                                    <i class="bi bi-trash3"></i>

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="8"
                        class="empty-state"
                    >

                        <div class="empty-icon">

                            <i class="bi bi-clipboard-x"></i>

                        </div>

                        <h5>
                            No Activity Logs Found
                        </h5>

                        <p>
                            No activities match your current filters.
                            Try changing your search criteria.
                        </p>

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    {{-- =================================================
         PAGINATION
    ================================================== --}}

    @if($logs->hasPages())

        <div class="pagination-wrapper">

            {{ $logs
                ->appends(request()->query())
                ->links('pagination::bootstrap-5')
            }}

        </div>

    @endif

</div>

</div>

</div>

{{-- =====================================================
LOG DETAIL MODAL
====================================================== --}}

<div
    class="modal fade"
    id="logModal"
    tabindex="-1"
    aria-hidden="true"
>


<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

    <div class="modal-content">

        <div class="modal-header">

            <h5 class="modal-title">

                <i class="bi bi-shield-check text-primary me-2"></i>

                Activity Log Details

            </h5>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
            ></button>

        </div>


        <div
            class="modal-body"
            id="logModalBody"
        >

            <div class="text-center py-5">

                <div
                    class="spinner-border text-primary"
                    role="status"
                ></div>

                <p class="text-muted mt-3 mb-0">
                    Loading activity details...
                </p>

            </div>

        </div>

    </div>

</div>


</div>

{{-- Bootstrap JS --}}

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

<script>

    /* =====================================================
       ESCAPE HTML
    ====================================================== */

    function escapeHtml(value)
    {
        if (
            value === null ||
            value === undefined
        ) {
            return '';
        }

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    /* =====================================================
       VIEW LOG
    ====================================================== */

    function viewLog(id)
    {

        const modalElement =
            document.getElementById('logModal');

        const modalBody =
            document.getElementById('logModalBody');


        modalBody.innerHTML = `

            <div class="text-center py-5">

                <div
                    class="spinner-border text-primary"
                ></div>

                <p class="text-muted mt-3 mb-0">

                    Loading activity details...

                </p>

            </div>

        `;


        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

        modal.show();


        fetch(
            `{{ url('/activity-logs') }}/${id}`,
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        )

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    'Failed to load activity log.'
                );

            }

            return response.json();

        })

        .then(data => {

            const log = data.log;


            let oldValues = '';

            let newValues = '';


            /* =================================================
               OLD VALUES
            ================================================== */

            if (log.old_values) {

                oldValues = `

                    <div class="mt-4">

                        <div class="detail-label mb-2">

                            Old Values

                        </div>

                        <pre class="values-box">${escapeHtml(

                            typeof log.old_values === 'string'

                                ? log.old_values

                                : JSON.stringify(
                                    log.old_values,
                                    null,
                                    2
                                )

                        )}</pre>


                </div>

            `;

        }


        /* =================================================
           NEW VALUES
        ================================================== */

        if (log.new_values) {

            newValues = `

                <div class="mt-4">

                    <div class="detail-label mb-2">

                        New Values

                    </div>

                    <pre class="values-box">${escapeHtml(

                        typeof log.new_values === 'string'

                            ? log.new_values

                            : JSON.stringify(
                                log.new_values,
                                null,
                                2
                            )

                    )}</pre>

                </div>

            `;

        }


        /* =================================================
           USER
        ================================================== */

        const userName =
            log.user?.name || 'System';


        /* =================================================
           MODEL
        ================================================== */

        let modelName = 'N/A';

        if (log.model_type) {

            modelName =
                log.model_type
                    .split('\\')
                    .pop();

        }


        /* =================================================
           MODAL CONTENT
        ================================================== */

        modalBody.innerHTML = `

            <div class="row g-3">

                <div class="col-md-6">

                    <div class="detail-card">

                        <div class="detail-label">
                            User
                        </div>

                        <div class="detail-value">

                            <i class="bi bi-person-circle me-1 text-primary"></i>

                            ${escapeHtml(userName)}

                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="detail-card">

                        <div class="detail-label">
                            User ID
                        </div>

                        <div class="detail-value">

                            ${escapeHtml(
                                log.user_id || 'System'
                            )}

                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="detail-card">

                        <div class="detail-label">
                            Action
                        </div>

                        <div class="detail-value">

                            ${escapeHtml(
                                String(log.action || '')
                                    .replace(/_/g, ' ')
                            )}

                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="detail-card">

                        <div class="detail-label">
                            Model
                        </div>

                        <div class="detail-value">

                            ${escapeHtml(modelName)}

                            <span class="text-muted">

                                #${escapeHtml(
                                    log.model_id || 'N/A'
                                )}

                            </span>

                        </div>

                    </div>

                </div>


                <div class="col-12">

                    <div class="detail-card">

                        <div class="detail-label">
                            Description
                        </div>

                        <div class="detail-value">

                            ${escapeHtml(
                                log.description || 'N/A'
                            )}

                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="detail-card">

                        <div class="detail-label">
                            IP Address
                        </div>

                        <div class="detail-value">

                            <code>

                                ${escapeHtml(
                                    log.ip_address || 'N/A'
                                )}

                            </code>

                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="detail-card">

                        <div class="detail-label">
                            Created At
                        </div>

                        <div class="detail-value">

                            ${escapeHtml(
                                log.created_at || 'N/A'
                            )}

                        </div>

                    </div>

                </div>


                <div class="col-12">

                    <div class="detail-card">

                        <div class="detail-label">
                            User Agent
                        </div>

                        <div class="detail-value text-muted">

                            ${escapeHtml(
                                log.user_agent || 'N/A'
                            )}

                        </div>

                    </div>

                </div>

            </div>

            ${oldValues}

            ${newValues}

        `;

    })

    .catch(error => {

        modalBody.innerHTML = `

            <div class="alert alert-danger">

                <i class="bi bi-exclamation-triangle me-2"></i>

                ${escapeHtml(error.message)}

            </div>

        `;

    });

}


</script>

</body>

</html>
