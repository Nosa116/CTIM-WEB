<?php
// admin/faith_clinic.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

define('FAITH_CLINIC_HTML_MODE', true);
require_once __DIR__ . '/../api/faith_clinic_init.php';

// Initial PHP queries for dashboard metrics
$totalCases = 0;
$totalMembers = 0;
$casesToday = 0;

$res1 = $conn->query("SELECT COUNT(*) as total FROM faith_clinic_cases");
if ($res1) {
    $row = $res1->fetch_assoc();
    $totalCases = (int)$row['total'];
}

$res2 = $conn->query("SELECT COUNT(*) as total FROM faith_clinic_members");
if ($res2) {
    $row = $res2->fetch_assoc();
    $totalMembers = (int)$row['total'];
}

$todayStr = date('Y-m-d');
$res3 = $conn->query("SELECT COUNT(*) as total FROM faith_clinic_cases WHERE case_date = '$todayStr'");
if ($res3) {
    $row = $res3->fetch_assoc();
    $casesToday = (int)$row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faith Clinic - Christ Temple</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Shared and Custom dashboard styles for Faith Clinic */
        .metric-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background-color: var(--surface-container-lowest);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            border: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .metric-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            background-color: rgba(0, 99, 152, 0.08);
        }

        .metric-details {
            display: flex;
            flex-direction: column;
        }

        .metric-num {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--on-surface);
            line-height: 1.2;
        }

        .metric-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--on-surface-variant);
        }

        /* Buttons & Utility Controls */
        .action-header-btns {
            display: flex;
            gap: 1rem;
        }

        .btn-primary {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary);
            color: white;
            border: 1px solid var(--primary);
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            background-color: #005F9E;
            border-color: #005F9E;
        }

        .btn-outline-primary {
            padding: 0.75rem 1.5rem;
            border: 1px solid var(--primary);
            background: transparent;
            color: var(--primary);
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-outline-primary:hover {
            background-color: rgba(0, 99, 152, 0.05);
        }

        /* Views Toolbar */
        .filters-toolbar {
            background-color: var(--surface-container-lowest);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .filters-row-top {
            display: flex;
            gap: 1rem;
            width: 100%;
        }

        .search-large {
            position: relative;
            flex: 1;
        }

        .search-large svg {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--outline);
        }

        .search-large input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            border: 1px solid var(--outline-variant);
            border-radius: 8px;
            outline: none;
            font-size: 0.95rem;
        }

        .search-large input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,99,152,0.1);
        }

        .date-filter-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .date-filter-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--on-surface-variant);
            white-space: nowrap;
        }

        .date-filter-group input {
            padding: 0.75rem;
            border: 1px solid var(--outline-variant);
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
            font-family: inherit;
        }

        .date-filter-group input:focus {
            border-color: var(--primary);
        }

        /* View Mode Switcher */
        .view-switcher-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--outline-variant);
            padding-top: 1rem;
        }

        .view-modes {
            display: flex;
            background-color: var(--surface-container-low);
            padding: 0.25rem;
            border-radius: 8px;
            gap: 0.25rem;
        }

        .view-mode-btn {
            border: none;
            background: transparent;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--on-surface-variant);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .view-mode-btn.active {
            background-color: var(--surface-container-lowest);
            color: var(--primary);
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }

        /* By Date: Chronological Timeline grouping */
        .timeline-container {
            position: relative;
            padding-left: 2rem;
            border-left: 2px solid var(--outline-variant);
            margin-left: 1rem;
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        .timeline-date-group {
            position: relative;
        }

        .timeline-date-header {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .timeline-dot {
            position: absolute;
            left: -2.55rem;
            top: 0.25rem;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: var(--primary);
            border: 3px solid var(--surface-container-lowest);
            box-shadow: 0 0 0 3px rgba(0,99,152,0.15);
        }

        .timeline-cards {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .timeline-card {
            background-color: var(--surface-container-lowest);
            border-radius: 10px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 2px 8px rgba(0,0,0,0.01);
        }

        .timeline-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--surface-container-high);
            padding-bottom: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .timeline-member-name {
            font-weight: 700;
            color: var(--on-surface);
            font-size: 1.05rem;
        }

        .timeline-member-contact {
            font-size: 0.85rem;
            color: var(--on-surface-variant);
        }

        .timeline-body-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .case-block-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .case-block-content {
            font-size: 0.95rem;
            line-height: 1.5;
            color: var(--on-surface-variant);
            background-color: var(--surface-container-low);
            padding: 0.75rem 1rem;
            border-radius: 6px;
            border-left: 3px solid var(--outline);
            white-space: pre-line;
        }

        .case-block-content.remark {
            border-left-color: var(--primary-accent);
            background-color: rgba(0, 99, 152, 0.02);
        }

        /* By Person: Directory and Nested history views */
        .person-directory {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 2rem;
            background-color: var(--surface-container-lowest);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.05);
            min-height: 500px;
            transition: grid-template-columns 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .person-directory.minimized-sidebar {
            grid-template-columns: 0px 1fr;
            gap: 0;
        }

        .person-list {
            border-right: 1px solid var(--outline-variant);
            padding-right: 1rem;
            max-height: 600px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 1;
            transform: translateX(0);
        }

        .person-directory.minimized-sidebar .person-list {
            opacity: 0;
            transform: translateX(-20px);
            padding: 0;
            border-right: none;
            pointer-events: none;
        }

        .person-item {
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            border: 1px solid transparent;
        }

        .person-item:hover {
            background-color: var(--surface-container-low);
        }

        .person-item.active {
            background-color: rgba(0, 99, 152, 0.08);
            border-color: rgba(0, 99, 152, 0.2);
            color: var(--primary);
        }

        .person-item-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.15rem;
        }

        .person-item-sub {
            font-size: 0.8rem;
            color: var(--on-surface-variant);
            display: flex;
            justify-content: space-between;
        }

        .person-detail-pane {
            padding-left: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            max-height: 600px;
            overflow-y: auto;
            transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .person-directory.minimized-sidebar .person-detail-pane {
            padding-left: 0;
        }

        /* Sidebar collapse visual toggling */
        .sidebar-toggle-row {
            display: none;
            margin-bottom: 0.5rem;
        }

        .person-directory.minimized-sidebar .sidebar-toggle-row {
            display: block;
        }

        .person-card-profile {
            background-color: var(--surface-container-low);
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .profile-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--on-surface);
            margin-bottom: 0.5rem;
        }

        .profile-meta-grid {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .profile-meta-item {
            font-size: 0.9rem;
            color: var(--on-surface-variant);
        }

        .profile-actions {
            display: flex;
            gap: 0.5rem;
        }

        .person-case-history {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .person-history-title {
            font-size: 1.1rem;
            font-weight: 700;
            border-bottom: 1px solid var(--outline-variant);
            padding-bottom: 0.5rem;
            color: var(--on-surface);
        }

        /* Glassmorphism Modals */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(6, 17, 31, 0.6);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background-color: var(--surface-container-lowest);
            border-radius: 16px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.1);
            transform: translateY(20px);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-overlay.active .modal-card {
            transform: translateY(0);
        }

        .modal-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--surface-container-high);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--on-surface);
        }

        .modal-close {
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--outline);
            transition: color 0.2s ease;
        }

        .modal-close:hover {
            color: var(--error);
        }

        .modal-body {
            padding: 2rem;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 1.25rem 2rem;
            border-top: 1px solid var(--surface-container-high);
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            background-color: var(--surface-container-low);
            border-radius: 0 0 16px 16px;
            flex-shrink: 0;
        }

        /* Modal Forms and Nested Controls */
        .modal-form {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex: 1;
        }

        .modal-form .form-group {
            margin-bottom: 1.25rem;
        }

        .modal-form label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--on-surface);
        }

        .modal-form input,
        .modal-form textarea,
        .modal-form select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--outline-variant);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
        }

        .modal-form input:focus,
        .modal-form textarea:focus,
        .modal-form select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,99,152,0.1);
        }

        .select-member-row {
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
        }

        .select-member-row select {
            flex: 1;
        }

        .searchable-select-container {
            flex: 1;
            position: relative;
        }

        .searchable-select-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--outline-variant);
            background: var(--surface-container-lowest);
            color: var(--on-surface);
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .searchable-select-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 99, 152, 0.15);
        }

        .dropdown-item-option:hover {
            background-color: rgba(0, 99, 152, 0.08) !important;
        }

        /* Inline Member Form inside Case Modal */
        .inline-member-register-box {
            background-color: var(--surface-container-low);
            border-radius: 8px;
            padding: 1.25rem;
            border: 1px dashed var(--outline-variant);
            margin-bottom: 1.25rem;
            display: none;
        }

        .inline-box-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
        }

        /* Case Reading Modal Details */
        .reading-modal-date {
            font-size: 0.85rem;
            color: var(--outline);
            margin-top: 0.25rem;
        }

        .reading-section-title {
            font-family: var(--font-serif);
            font-size: 1.15rem;
            font-style: italic;
            color: var(--primary);
            margin-bottom: 0.5rem;
            border-left: 3px solid var(--primary-accent);
            padding-left: 0.5rem;
        }

        .reading-text-pane {
            font-size: 1.05rem;
            line-height: 1.6;
            color: var(--on-surface-variant);
            background-color: var(--surface-container-low);
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            white-space: pre-line;
        }

        /* Empty States */
        .empty-placeholder {
            padding: 4rem 2rem;
            text-align: center;
            color: var(--outline);
            background-color: var(--surface-container-lowest);
            border-radius: 12px;
            border: 1px dashed var(--outline-variant);
        }

        .empty-placeholder svg {
            margin-bottom: 1rem;
            color: var(--outline-variant);
        }

        /* Grid of tiles for member cases */
        .person-cases-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.25rem;
            margin-top: 1rem;
        }

        .case-tile {
            background-color: var(--surface-container-low);
            border-radius: 10px;
            padding: 1.25rem;
            border: 1px solid rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
            height: 220px;
        }

        .case-tile:hover {
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
            transform: translateY(-2px);
            background-color: var(--surface-container-lowest);
            border-color: rgba(0,99,152,0.15);
        }

        .case-tile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .case-tile-date {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.95rem;
        }

        .case-tile-body {
            font-size: 0.85rem;
            color: var(--on-surface-variant);
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.75rem;
            flex-grow: 1;
            font-style: italic;
        }

        .case-tile-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--surface-container-high);
            padding-top: 0.75rem;
            margin-top: auto;
        }
    </style>
</head>
<body class="admin-dashboard">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <a href="../index.html">
                <img src="../assets/images/Christ Temple logo.svg" alt="Christ Temple">
            </a>
            <span>Admin</span>
        </div>
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Sermons
            </a>
            <a href="faith_clinic.php" class="admin-nav-link active">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Faith Clinic
            </a>
            <a href="logout.php" class="admin-nav-link logout-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <header class="admin-header">
            <div class="header-title">
                <h1>Faith Clinic Platform</h1>
                <p>Register members, narrations, and counseling remarks</p>
            </div>
            <div class="action-header-btns">
                <button class="btn-outline-primary" id="btnOpenMemberModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Register Member
                </button>
                <button class="btn-primary" id="btnOpenCaseModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Case Record
                </button>
            </div>
        </header>

        <div class="admin-content">
            <!-- Metrics Panels -->
            <section class="metric-cards-grid">
                <div class="metric-card">
                    <div class="metric-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="metric-details">
                        <span class="metric-num" id="statTotalCases"><?php echo $totalCases; ?></span>
                        <span class="metric-label">Total Cases Saved</span>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="metric-details">
                        <span class="metric-num" id="statTotalMembers"><?php echo $totalMembers; ?></span>
                        <span class="metric-label">Registered Members</span>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                    <div class="metric-details">
                        <span class="metric-num" id="statCasesToday"><?php echo $casesToday; ?></span>
                        <span class="metric-label">Sessions Today</span>
                    </div>
                </div>
            </section>

            <!-- Search, Filters, and View Toggles Toolbar -->
            <section class="filters-toolbar">
                <div class="filters-row-top">
                    <div class="search-large">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="mainSearchInput" placeholder="Search cases by member name, phone, address, narration content, or remark keyword...">
                    </div>

                    <div class="date-filter-group">
                        <label for="filterDateStart">From:</label>
                        <input type="date" id="filterDateStart">
                        <label for="filterDateEnd">To:</label>
                        <input type="date" id="filterDateEnd">
                        <button class="icon-btn" id="btnClearFilters" title="Clear Filters" style="width: auto; padding: 0.5rem 1rem;">
                            Reset
                        </button>
                    </div>
                </div>

                <div class="view-switcher-row">
                    <div class="view-modes">
                        <button class="view-mode-btn active" id="viewModeCases" data-mode="cases">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            By Cases
                        </button>
                        <button class="view-mode-btn" id="viewModeDate" data-mode="date">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            By Date
                        </button>
                        <button class="view-mode-btn" id="viewModePerson" data-mode="person">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            By Person
                        </button>
                    </div>

                    <div style="font-size: 0.9rem; color: var(--on-surface-variant);" id="filterResultsCount">
                        Loading...
                    </div>
                </div>
            </section>

            <!-- Dynamic View Output Panel -->
            <section class="admin-card" id="outputPanel" style="padding: 1.5rem;">
                <!-- Content injected dynamically via JS based on active tab -->
            </section>
        </div>
    </main>

    <!-- Modal 1: Register/Edit Member Modal -->
    <div class="modal-overlay" id="memberModalOverlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="memberModalTitle">Register New Member</h3>
                <button class="modal-close" id="btnCloseMemberModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form id="memberForm" class="modal-form">
                <div class="modal-body">
                    <input type="hidden" id="memberFormId" name="id" value="0">
                    <div class="form-group">
                        <label for="memberName">Full Name *</label>
                        <input type="text" id="memberName" name="name" placeholder="e.g. Brother John Doe" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="memberPhone">Phone Number</label>
                        <input type="tel" id="memberPhone" name="phone" placeholder="e.g. +234 80 1234 5678" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="memberAddress">Home Address</label>
                        <textarea id="memberAddress" name="address" rows="3" placeholder="e.g. No 15, Grace Avenue, Lagos" autocomplete="off"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-primary" id="btnCancelMemberModal" style="border-color:var(--outline-variant); color:var(--on-surface-variant);">Cancel</button>
                    <button type="submit" class="btn-primary" id="btnSubmitMemberForm">Save Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Create/Edit Case Record Modal -->
    <div class="modal-overlay" id="caseModalOverlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="caseModalTitle">Log New Case Record</h3>
                <button class="modal-close" id="btnCloseCaseModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form id="caseForm" class="modal-form">
                <div class="modal-body">
                    <input type="hidden" id="caseFormId" name="id" value="0">

                    <!-- Inline Quick Member Registration form inside Case modal -->
                    <div class="inline-member-register-box" id="inlineMemberBox">
                        <div class="inline-box-title">
                            <span>Register Member on-the-fly</span>
                            <button type="button" class="modal-close" id="btnCancelInlineMember" style="color:var(--error);" title="Discard inline form">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="form-group">
                            <label for="inlineName">Full Name *</label>
                            <input type="text" id="inlineName" placeholder="e.g. Sister Mary Smith" autocomplete="off">
                        </div>
                        <div style="display:flex; gap:1rem; margin-bottom: 0.5rem;">
                            <div style="flex:1;">
                                <label for="inlinePhone">Phone</label>
                                <input type="tel" id="inlinePhone" placeholder="e.g. +234 81 2222 3333">
                            </div>
                            <div style="flex:1.5;">
                                <label for="inlineAddress">Address</label>
                                <input type="text" id="inlineAddress" placeholder="e.g. 5 Hope Rd">
                            </div>
                        </div>
                        <button type="button" class="btn-primary" id="btnSaveInlineMember" style="padding:0.5rem 1rem; font-size:0.85rem; border-radius:6px; margin-top:0.5rem;">
                            Register & Select
                        </button>
                    </div>

                    <div class="form-group" id="memberSelectionGroup">
                        <label>Select Member *</label>
                        <div class="select-member-row">
                            <div class="searchable-select-container">
                                <input type="text" id="memberSearchInput" class="searchable-select-input" placeholder="Type to search and select member..." autocomplete="off">
                                <input type="hidden" id="caseMemberSelect" name="member_id" required>
                                <div class="searchable-select-dropdown" id="memberDropdownOptions" style="display:none; position:absolute; top:100%; left:0; width:100%; max-height:220px; overflow-y:auto; background:var(--surface-container-lowest); border:1px solid var(--outline-variant); border-radius:8px; z-index:1000; box-shadow:0 8px 24px rgba(0,0,0,0.15); margin-top:4px;">
                                    <!-- Options populated dynamically -->
                                </div>
                            </div>
                            <button type="button" class="btn-outline-primary" id="btnShowInlineMember" title="Register a new member first without leaving this screen" style="padding:0.75rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="caseDateInput">Case Date (Defaults to Today) *</label>
                        <input type="date" id="caseDateInput" name="case_date" required>
                    </div>

                    <div class="form-group">
                        <label for="caseNarration">Member's Narration *</label>
                        <textarea id="caseNarration" name="narration" rows="5" placeholder="Document the member's confession, testimony, problem description, or prayer requests..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="caseRemark">Counselor / Clinic Remarks *</label>
                        <textarea id="caseRemark" name="remark" rows="4" placeholder="Document the remarks, scriptures shared, diagnostic inputs, or prayer directives..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-primary" id="btnCancelCaseModal" style="border-color:var(--outline-variant); color:var(--on-surface-variant);">Cancel</button>
                    <button type="submit" class="btn-primary" id="btnSubmitCaseForm">Save Case</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Reading Pane / Detail View Modal -->
    <div class="modal-overlay" id="readModalOverlay">
        <div class="modal-card" style="max-width: 700px;">
            <div class="modal-header" style="background-color: var(--surface-container-low);">
                <div>
                    <h3 class="modal-title" id="readMemberName">John Doe</h3>
                    <div class="reading-modal-date" id="readCaseDate">Wed, May 27th 2026</div>
                </div>
                <button class="modal-close" id="btnCloseReadModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="modal-body" style="padding: 2.5rem 2rem;">
                <div style="margin-bottom:1.5rem; font-size:0.9rem; color:var(--on-surface-variant); line-height:1.4;">
                    <strong>Member Profile:</strong>
                    <div id="readMemberContact">Phone: N/A | Address: N/A</div>
                </div>

                <h4 class="reading-section-title">Member's Narration</h4>
                <div class="reading-text-pane" id="readNarration">
                    Waiting for data...
                </div>

                <h4 class="reading-section-title">Counselor's remarks</h4>
                <div class="reading-text-pane remark" id="readRemark">
                    Waiting for data...
                </div>
            </div>
            <div class="modal-footer" style="padding:1rem 2rem;">
                <button type="button" class="btn-primary" id="btnCloseReadPane" style="padding: 0.6rem 1.5rem;">Close Reading View</button>
            </div>
        </div>
    </div>

    <script>
        // CLIENT STATE MANAGEMENT
        let state = {
            viewMode: 'cases', // 'cases', 'date', 'person'
            cases: [],
            members: [],
            selectedPersonId: null, // Tracks active member folder in By Person view
            sidebarMinimized: false, // Tracks directory panel minimized state
            filters: {
                search: '',
                startDate: '',
                endDate: ''
            }
        };

        // DOM ELEMENTS & HANDLERS REGISTER
        const out = document.getElementById('outputPanel');
        const countSpan = document.getElementById('filterResultsCount');

        // Modal triggers
        const caseModal = document.getElementById('caseModalOverlay');
        const memberModal = document.getElementById('memberModalOverlay');
        const readModal = document.getElementById('readModalOverlay');

        // In-form elements
        const selectMember = document.getElementById('caseMemberSelect');
        const inlineBox = document.getElementById('inlineMemberBox');

        // Initial DOM Load Trigger
        document.addEventListener('DOMContentLoaded', () => {
            // Default dates
            document.getElementById('caseDateInput').value = new Date().toISOString().split('T')[0];

            // Load data
            refreshDashboardData();

            // Set up views & event listeners
            setupEventListeners();
        });

        // REFRESH CORE DATA PIPELINE
        async function refreshDashboardData() {
            try {
                // Fetch cases and members asynchronously
                const [casesRes, membersRes] = await Promise.all([
                    fetch('../api/get_faith_clinic_cases.php'),
                    fetch('../api/get_faith_clinic_members.php')
                ]);

                const casesData = await casesRes.json();
                const membersData = await membersRes.json();

                if (casesData.status === 'success') {
                    state.cases = casesData.data;
                }
                if (membersData.status === 'success') {
                    state.members = membersData.data;
                }

                // Render metrics stats on demand
                updateMetricsDisplay();

                // Re-populate Member Select dropdowns
                repopulateMemberDropdown();

                // Apply active filters and render
                applyFiltersAndRender();
            } catch (err) {
                console.error("Error syncing dashboard state:", err);
            }
        }

        // DYNAMIC VIEW ENGINE
        function applyFiltersAndRender() {
            // 1. Filter local state array based on state.filters
            let filtered = state.cases;

            if (state.filters.search.trim() !== '') {
                const q = state.filters.search.toLowerCase().trim();
                filtered = filtered.filter(c => {
                    const name = c.member_name ? c.member_name.toLowerCase() : '';
                    const phone = c.member_phone ? c.member_phone.toLowerCase() : '';
                    const addr = c.member_address ? c.member_address.toLowerCase() : '';
                    const narr = c.narration ? c.narration.toLowerCase() : '';
                    const rem = c.remark ? c.remark.toLowerCase() : '';
                    
                    return name.includes(q) ||
                           phone.includes(q) ||
                           addr.includes(q) ||
                           narr.includes(q) ||
                           rem.includes(q);
                });
            }

            if (state.filters.startDate !== '') {
                filtered = filtered.filter(c => c.raw_date >= state.filters.startDate);
            }

            if (state.filters.endDate !== '') {
                filtered = filtered.filter(c => c.raw_date <= state.filters.endDate);
            }

            // Update result counter display
            countSpan.textContent = `Showing ${filtered.length} of ${state.cases.length} entries`;

            // 2. Render depending on the active viewMode
            if (state.viewMode === 'cases') {
                renderCasesView(filtered);
            } else if (state.viewMode === 'date') {
                renderDateView(filtered);
            } else if (state.viewMode === 'person') {
                renderPersonView(filtered);
            }
        }

        // VIEW 1: DYNAMIC CASES TABLE
        function renderCasesView(data) {
            if (data.length === 0) {
                out.innerHTML = `
                    <div class="empty-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <h3>No Cases Found</h3>
                        <p>Adjust your search criteria or register a new Faith Clinic consultation record.</p>
                    </div>`;
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 140px;">Date</th>
                                <th>Member Profile</th>
                                <th>Member Narration snippet</th>
                                <th>Counselor Remark snippet</th>
                                <th style="width: 140px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>`;

            data.forEach(item => {
                const narrSnippet = item.narration.length > 80 ? item.narration.substring(0, 80) + '...' : item.narration;
                const remSnippet = item.remark.length > 80 ? item.remark.substring(0, 80) + '...' : item.remark;
                const phone = item.member_phone ? item.member_phone : 'N/A';

                html += `
                    <tr>
                        <td>
                            <strong style="color:var(--primary); font-size:0.9rem;">${item.formatted_date}</strong>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--on-surface);">${item.member_name}</div>
                            <div style="font-size:0.8rem; color: var(--on-surface-variant);">Phone: ${phone}</div>
                        </td>
                        <td style="color: var(--on-surface-variant); font-size:0.9rem;">
                            <em>"${escapeHTML(narrSnippet)}"</em>
                        </td>
                        <td style="color: var(--on-surface-variant); font-size:0.9rem;">
                            <span>${escapeHTML(remSnippet)}</span>
                        </td>
                        <td>
                            <div class="action-btns" style="justify-content: flex-end;">
                                <button class="icon-btn edit" onclick="viewCaseDetails(${item.id})" title="Read Details/Expand">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </button>
                                <button class="icon-btn" onclick="editCase(${item.id})" title="Edit Form">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button class="icon-btn delete" onclick="deleteCase(${item.id})" title="Delete entry">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>`;
            });

            html += `</tbody></table></div>`;
            out.innerHTML = html;
        }

        // VIEW 2: GROUPED TIMELINE BY DATE
        function renderDateView(data) {
            if (data.length === 0) {
                out.innerHTML = `
                    <div class="empty-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z" /></svg>
                        <h3>No Cases Found</h3>
                        <p>Adjust your search criteria or register a new Faith Clinic consultation record.</p>
                    </div>`;
                return;
            }

            // Group data by date
            const groups = {};
            data.forEach(item => {
                const groupName = item.formatted_date;
                if (!groups[groupName]) {
                    groups[groupName] = [];
                }
                groups[groupName].push(item);
            });

            let html = `<div class="timeline-container">`;

            for (const [dateHeader, cases] of Object.entries(groups)) {
                html += `
                    <div class="timeline-date-group">
                        <div class="timeline-dot"></div>
                        <h3 class="timeline-date-header">${dateHeader}</h3>
                        <div class="timeline-cards">`;

                cases.forEach(c => {
                    const phone = c.member_phone ? c.member_phone : 'No Phone number';
                    html += `
                        <div class="timeline-card">
                            <div class="timeline-card-header">
                                <div>
                                    <span class="timeline-member-name">${c.member_name}</span>
                                    <span class="timeline-member-contact" style="margin-left: 1rem;">Phone: ${phone}</span>
                                </div>
                                <div class="action-btns">
                                    <button class="icon-btn edit" onclick="viewCaseDetails(${c.id})" title="Read Details/Expand">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                    <button class="icon-btn" onclick="editCase(${c.id})" title="Edit Form">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button class="icon-btn delete" onclick="deleteCase(${c.id})" title="Delete entry">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="timeline-body-grid">
                                <div>
                                    <div class="case-block-label">Member's Narration</div>
                                    <div class="case-block-content">${escapeHTML(c.narration)}</div>
                                </div>
                                <div>
                                    <div class="case-block-label">Remarks & spiritual directives</div>
                                    <div class="case-block-content remark">${escapeHTML(c.remark)}</div>
                                </div>
                            </div>
                        </div>`;
                });

                html += `</div></div>`;
            }

            html += `</div>`;
            out.innerHTML = html;
        }

        // VIEW 3: CASE HISTORIES GROUPED BY MEMBER (PERSON DIR)
        function renderPersonView(filteredCases) {
            if (state.members.length === 0) {
                out.innerHTML = `
                    <div class="empty-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        <h3>No Members Registered</h3>
                        <p>Start by registering your church clinic members to view their individual timeline portfolios.</p>
                    </div>`;
                return;
            }

            // Filter directory members dynamically based on the search query
            // Filter directory members dynamically based on the search query
            let visibleMembers = state.members;
            if (state.filters.search.trim() !== '') {
                const q = state.filters.search.toLowerCase().trim();
                visibleMembers = state.members.filter(m => {
                    const name = m.name ? m.name.toLowerCase() : '';
                    const phone = m.phone ? m.phone.toLowerCase() : '';
                    const address = m.address ? m.address.toLowerCase() : '';
                    
                    const profileMatches = name.includes(q) || phone.includes(q) || address.includes(q);
                    const hasFilteredCases = filteredCases.some(c => c.member_id == m.id);
                    
                    return profileMatches || hasFilteredCases;
                });
            }

            // Resolve the active selected member among the visible list
            let activeMemberId = state.selectedPersonId;
            if (visibleMembers.length > 0) {
                const isSelectedVisible = visibleMembers.some(m => m.id == state.selectedPersonId);
                if (!isSelectedVisible) {
                    activeMemberId = visibleMembers[0].id;
                }
            } else {
                activeMemberId = null;
            }

            let sidebarHtml = `
                <div class="person-list">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:0.5rem; border-bottom:1px solid var(--outline-variant);">
                        <strong style="color:var(--primary); font-size:0.8rem; letter-spacing:0.05em; text-transform:uppercase;">Members</strong>
                        <button class="icon-btn" onclick="toggleDirectorySidebar(false)" title="Hide List" style="width:24px; height:24px; display:flex; align-items:center; justify-content:center; border:none; font-size:0.8rem; cursor:pointer;">
                            ◀
                        </button>
                    </div>`;

            if (visibleMembers.length === 0) {
                sidebarHtml += `
                    <div style="padding:1.5rem 1rem; text-align:center; color:var(--on-surface-variant); font-size:0.85rem;">
                        No matching members found.
                    </div>`;
            } else {
                visibleMembers.forEach(m => {
                    const isActive = m.id == activeMemberId ? 'active' : '';
                    sidebarHtml += `
                        <div class="person-item ${isActive}" onclick="selectPerson(${m.id})">
                            <div class="person-item-name">${m.name}</div>
                            <div class="person-item-sub">
                                <span>Phone: ${m.phone ? m.phone : 'N/A'}</span>
                                <span class="badge" style="padding:0.1rem 0.4rem; font-size:0.65rem;">${m.case_count} cases</span>
                            </div>
                        </div>`;
                });
            }
            sidebarHtml += `</div>`;

            // Active member profile details
            const activeMember = activeMemberId ? visibleMembers.find(m => m.id == activeMemberId) : null;
            let detailHtml = `<div class="person-detail-pane">`;

            if (activeMember) {
                const casesForMember = filteredCases.filter(c => c.member_id == activeMember.id);
                const phone = activeMember.phone ? activeMember.phone : 'N/A';
                const addr = activeMember.address ? activeMember.address : 'No Address provided';

                detailHtml += `
                    <!-- Toggle trigger shown when sidebar is minimized -->
                    <div class="sidebar-toggle-row">
                        <button class="btn-outline-primary" style="padding:0.45rem 1rem; font-size:0.75rem; border-radius:6px; width:auto; border-color:var(--outline-variant); color:var(--primary);" onclick="toggleDirectorySidebar(true)">
                            📂 Show Members Directory
                        </button>
                    </div>

                    <div class="person-card-profile">
                        <div>
                            <h2 class="profile-title">${activeMember.name}</h2>
                            <div class="profile-meta-grid">
                                <span class="profile-meta-item">📞 <strong>Phone:</strong> ${phone}</span>
                                <span class="profile-meta-item">🏠 <strong>Address:</strong> ${addr}</span>
                            </div>
                        </div>
                        <div class="profile-actions">
                            <button class="btn-primary" style="padding:0.5rem 1rem; font-size:0.85rem; width:auto;" onclick="openNewCaseForMember(${activeMember.id})">
                                New Case
                            </button>
                            <button class="btn-outline-primary" style="padding:0.5rem 1rem; font-size:0.85rem;" onclick="editMember(${activeMember.id})">
                                Edit Profile
                            </button>
                            <button class="btn-outline-primary" style="padding:0.5rem 1rem; font-size:0.85rem; border-color:var(--error); color:var(--error);" onclick="deleteMember(${activeMember.id})">
                                Delete Member
                            </button>
                        </div>
                    </div>

                    <div class="person-case-history">
                        <h4 class="person-history-title">Case Consultation Log (${casesForMember.length})</h4>`;

                if (casesForMember.length === 0) {
                    detailHtml += `
                        <div class="empty-placeholder" style="padding: 2rem;">
                            <p>No active case entries logged for this member matching the current search parameters.</p>
                        </div>`;
                } else {
                    detailHtml += `<div class="person-cases-grid">`;
                    casesForMember.forEach(c => {
                        detailHtml += `
                            <div class="case-tile">
                                <div class="case-tile-header">
                                    <span class="case-tile-date">${c.formatted_date}</span>
                                </div>
                                <div class="case-tile-body">
                                    "${escapeHTML(c.narration)}"
                                </div>
                                <div class="case-tile-footer">
                                    <button class="btn-primary" style="padding:0.4rem 0.8rem; font-size:0.75rem; border-radius:6px; width:auto;" onclick="viewCaseDetails(${c.id})">
                                        Expand
                                    </button>
                                    <div class="action-btns">
                                        <button class="icon-btn" onclick="editCase(${c.id})" title="Edit" style="width:28px; height:28px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button class="icon-btn delete" onclick="deleteCase(${c.id})" title="Delete" style="width:28px; height:28px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                    });
                    detailHtml += `</div>`;
                }

                detailHtml += `</div>`;
            } else {
                detailHtml += `
                    <div class="empty-placeholder" style="padding: 3rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <h3>No matching members found</h3>
                        <p>Adjust your search criteria to see member profiles and case history.</p>
                    </div>`;
            }

            detailHtml += `</div>`;

            const isMinimized = state.sidebarMinimized ? 'minimized-sidebar' : '';

            // Combine both inside grid
            out.innerHTML = `
                <div class="person-directory ${isMinimized}">
                    ${sidebarHtml}
                    ${detailHtml}
                </div>`;
        }

        function selectPerson(id) {
            state.selectedPersonId = id;
            state.sidebarMinimized = true; // Auto-minimize on click to show cases and info
            applyFiltersAndRender();
        }

        function openNewCaseForMember(memberId) {
            document.getElementById('caseFormId').value = "0";
            document.getElementById('caseForm').reset();
            document.getElementById('caseDateInput').value = new Date().toISOString().split('T')[0];
            document.getElementById('caseModalTitle').textContent = "Log New Case Record";
            inlineBox.style.display = 'none';
            document.getElementById('memberSelectionGroup').style.display = 'block';
            
            setSelectedMember(memberId);
            
            caseModal.classList.add('active');
        }

        function setSelectedMember(memberId) {
            const hiddenInput = document.getElementById('caseMemberSelect');
            const textInput = document.getElementById('memberSearchInput');
            if (!hiddenInput || !textInput) return;

            if (!memberId) {
                hiddenInput.value = "";
                textInput.value = "";
                return;
            }

            const member = state.members.find(m => m.id == memberId);
            if (member) {
                hiddenInput.value = member.id;
                const phoneStr = member.phone ? ` (${member.phone})` : '';
                textInput.value = `${member.name}${phoneStr}`;
            } else {
                hiddenInput.value = "";
                textInput.value = "";
            }
        }

        function selectMemberOption(memberId) {
            setSelectedMember(memberId);
            const dropdown = document.getElementById('memberDropdownOptions');
            if (dropdown) dropdown.style.display = 'none';
        }

        function renderDropdownOptions(filterText = '') {
            const optionsContainer = document.getElementById('memberDropdownOptions');
            if (!optionsContainer) return;

            const q = filterText.toLowerCase().trim();
            const filteredMembers = state.members.filter(m => {
                const name = m.name ? m.name.toLowerCase() : '';
                const phone = m.phone ? m.phone.toLowerCase() : '';
                return name.includes(q) || phone.includes(q);
            });

            // Sort members alphabetically
            filteredMembers.sort((a,b) => a.name.localeCompare(b.name));

            if (filteredMembers.length === 0) {
                optionsContainer.innerHTML = `
                    <div style="padding:0.75rem 1rem; color:var(--on-surface-variant); font-size:0.85rem; text-align:center;">
                        No members found
                    </div>`;
                return;
            }

            let html = '';
            filteredMembers.forEach(m => {
                const phoneStr = m.phone ? ` (${m.phone})` : '';
                html += `
                    <div class="dropdown-item-option" 
                         style="padding:0.75rem 1rem; cursor:pointer; font-size:0.9rem; transition: background 0.2s;" 
                         onclick="selectMemberOption(${m.id})">
                         <div style="font-weight:600; color:var(--on-surface);">${m.name}</div>
                         <div style="font-size:0.75rem; color:var(--on-surface-variant);">${m.phone ? m.phone : 'No Phone'}</div>
                    </div>`;
            });
            optionsContainer.innerHTML = html;
        }

        function toggleDirectorySidebar(show) {
            state.sidebarMinimized = !show;
            applyFiltersAndRender();
        }

        // METRICS COUNTER ANIMATION & UPDATE
        function updateMetricsDisplay() {
            document.getElementById('statTotalCases').textContent = state.cases.length;
            document.getElementById('statTotalMembers').textContent = state.members.length;

            const todayStr = new Date().toISOString().split('T')[0];
            const casesToday = state.cases.filter(c => c.raw_date === todayStr).length;
            document.getElementById('statCasesToday').textContent = casesToday;
        }

        // DROP-DOWN POPULATION SELECTS
        function repopulateMemberDropdown() {
            const hiddenInput = document.getElementById('caseMemberSelect');
            const currVal = hiddenInput ? hiddenInput.value : '';
            
            if (currVal) {
                setSelectedMember(currVal);
            } else {
                setSelectedMember('');
            }

            renderDropdownOptions('');
        }

        // EVENT LISTENERS CONTROLLER
        function setupEventListeners() {
            // View Mode Tab toggles
            document.querySelectorAll('.view-mode-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    document.querySelectorAll('.view-mode-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    state.viewMode = btn.dataset.mode;
                    applyFiltersAndRender();
                });
            });

            // Live filters
            document.getElementById('mainSearchInput').addEventListener('input', (e) => {
                state.filters.search = e.target.value;
                applyFiltersAndRender();
            });

            document.getElementById('filterDateStart').addEventListener('change', (e) => {
                state.filters.startDate = e.target.value;
                applyFiltersAndRender();
            });

            document.getElementById('filterDateEnd').addEventListener('change', (e) => {
                state.filters.endDate = e.target.value;
                applyFiltersAndRender();
            });

            document.getElementById('btnClearFilters').addEventListener('click', () => {
                document.getElementById('mainSearchInput').value = '';
                document.getElementById('filterDateStart').value = '';
                document.getElementById('filterDateEnd').value = '';
                state.filters.search = '';
                state.filters.startDate = '';
                state.filters.endDate = '';
                applyFiltersAndRender();
            });

            // Member Modal buttons
            document.getElementById('btnOpenMemberModal').addEventListener('click', () => {
                document.getElementById('memberFormId').value = "0";
                document.getElementById('memberForm').reset();
                document.getElementById('memberModalTitle').textContent = "Register New Member";
                memberModal.classList.add('active');
            });

            document.getElementById('btnCloseMemberModal').addEventListener('click', () => memberModal.classList.remove('active'));
            document.getElementById('btnCancelMemberModal').addEventListener('click', () => memberModal.classList.remove('active'));

            // Case Modal buttons
            document.getElementById('btnOpenCaseModal').addEventListener('click', () => {
                document.getElementById('caseFormId').value = "0";
                document.getElementById('caseForm').reset();
                setSelectedMember('');
                document.getElementById('caseDateInput').value = new Date().toISOString().split('T')[0];
                document.getElementById('caseModalTitle').textContent = "Log New Case Record";
                inlineBox.style.display = 'none';
                document.getElementById('memberSelectionGroup').style.display = 'block';
                caseModal.classList.add('active');
            });

            // Custom Searchable Dropdown events
            const searchInput = document.getElementById('memberSearchInput');
            const dropdown = document.getElementById('memberDropdownOptions');

            if (searchInput && dropdown) {
                // Focus: show dropdown and render all options
                searchInput.addEventListener('focus', () => {
                    dropdown.style.display = 'block';
                    renderDropdownOptions(searchInput.value);
                });

                // Input typing: filter options
                searchInput.addEventListener('input', (e) => {
                    dropdown.style.display = 'block';
                    renderDropdownOptions(e.target.value);
                });

                // Click outside: hide dropdown
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.searchable-select-container') && !e.target.closest('#memberDropdownOptions')) {
                        dropdown.style.display = 'none';
                        // If they clicked away without choosing, and hidden input is set, restore the display
                        const hiddenInput = document.getElementById('caseMemberSelect');
                        if (hiddenInput && hiddenInput.value) {
                            setSelectedMember(hiddenInput.value);
                        } else {
                            searchInput.value = '';
                        }
                    }
                });
            }

            document.getElementById('btnCloseCaseModal').addEventListener('click', () => caseModal.classList.remove('active'));
            document.getElementById('btnCancelCaseModal').addEventListener('click', () => caseModal.classList.remove('active'));

            // Inline quick member toggle inside case modal
            document.getElementById('btnShowInlineMember').addEventListener('click', () => {
                inlineBox.style.display = 'block';
                document.getElementById('memberSelectionGroup').style.display = 'none';
                document.getElementById('inlineName').focus();
            });

            document.getElementById('btnCancelInlineMember').addEventListener('click', () => {
                inlineBox.style.display = 'none';
                document.getElementById('memberSelectionGroup').style.display = 'block';
            });

            // Save Inline Member
            document.getElementById('btnSaveInlineMember').addEventListener('click', async () => {
                const name = document.getElementById('inlineName').value.trim();
                const phone = document.getElementById('inlinePhone').value.trim();
                const address = document.getElementById('inlineAddress').value.trim();

                if (!name) {
                    alert("Please provide the member's full name.");
                    return;
                }

                try {
                    const res = await fetch('../api/save_faith_clinic_member.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ name, phone, address })
                    });
                    const r = await res.json();

                    if (r.status === 'success') {
                        // Reload state
                        await refreshDashboardData();

                        // Select newly created member
                        setSelectedMember(r.data.id);

                        // Reset & Close inline form
                        document.getElementById('inlineName').value = '';
                        document.getElementById('inlinePhone').value = '';
                        document.getElementById('inlineAddress').value = '';
                        inlineBox.style.display = 'none';
                        document.getElementById('memberSelectionGroup').style.display = 'block';
                    } else {
                        alert("Error registering member: " + r.message);
                    }
                } catch (e) {
                    console.error(e);
                    alert("Failed to register member inline.");
                }
            });

            // Reading pane Close button
            document.getElementById('btnCloseReadModal').addEventListener('click', () => readModal.classList.remove('active'));
            document.getElementById('btnCloseReadPane').addEventListener('click', () => readModal.classList.remove('active'));

            // FORM SUBMISSIONS VIA AJAX
            // 1. Submit Member Form
            document.getElementById('memberForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const fd = new FormData(e.target);
                const obj = Object.fromEntries(fd.entries());
                const btn = document.getElementById('btnSubmitMemberForm');

                btn.textContent = 'Saving...';
                btn.disabled = true;

                try {
                    const res = await fetch('../api/save_faith_clinic_member.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(obj)
                    });
                    const r = await res.json();

                    if (r.status === 'success') {
                        memberModal.classList.remove('active');
                        await refreshDashboardData();
                    } else {
                        alert("Error: " + r.message);
                    }
                } catch (err) {
                    console.error(err);
                    alert("An unexpected error occurred.");
                } finally {
                    btn.textContent = 'Save Member';
                    btn.disabled = false;
                }
            });

            // 2. Submit Case Form
            document.getElementById('caseForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const fd = new FormData(e.target);
                const obj = Object.fromEntries(fd.entries());
                const btn = document.getElementById('btnSubmitCaseForm');

                btn.textContent = 'Saving...';
                btn.disabled = true;

                try {
                    const res = await fetch('../api/save_faith_clinic_case.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(obj)
                    });
                    const r = await res.json();

                    if (r.status === 'success') {
                        caseModal.classList.remove('active');
                        await refreshDashboardData();
                    } else {
                        alert("Error: " + r.message);
                    }
                } catch (err) {
                    console.error(err);
                    alert("An unexpected error occurred.");
                } finally {
                    btn.textContent = 'Save Case';
                    btn.disabled = false;
                }
            });
        }

        // AUXILIARY LOGIC ACTIONS: EDIT, DELETE, DETAIL VIEW
        function viewCaseDetails(caseId) {
            const item = state.cases.find(c => c.id == caseId);
            if (!item) return;

            document.getElementById('readMemberName').textContent = item.member_name;
            document.getElementById('readCaseDate').textContent = item.formatted_date;
            
            const phone = item.member_phone ? item.member_phone : 'N/A';
            const addr = item.member_address ? item.member_address : 'N/A';
            document.getElementById('readMemberContact').innerHTML = `<strong>Phone:</strong> ${phone} &nbsp;|&nbsp; <strong>Address:</strong> ${addr}`;

            document.getElementById('readNarration').textContent = item.narration;
            document.getElementById('readRemark').textContent = item.remark;

            readModal.classList.add('active');
        }

        function editCase(caseId) {
            const item = state.cases.find(c => c.id == caseId);
            if (!item) return;

            document.getElementById('caseFormId').value = item.id;
            setSelectedMember(item.member_id);
            document.getElementById('caseDateInput').value = item.raw_date;
            document.getElementById('caseNarration').value = item.narration;
            document.getElementById('caseRemark').value = item.remark;

            document.getElementById('caseModalTitle').textContent = "Edit Case Record";
            inlineBox.style.display = 'none';
            document.getElementById('memberSelectionGroup').style.display = 'block';

            caseModal.classList.add('active');
        }

        async function deleteCase(caseId) {
            if (!confirm("Are you sure you want to permanently delete this clinic case record? This cannot be undone.")) {
                return;
            }

            try {
                const res = await fetch(`../api/delete_faith_clinic_case.php?id=${caseId}`);
                const r = await res.json();
                if (r.status === 'success') {
                    await refreshDashboardData();
                } else {
                    alert("Delete failed: " + r.message);
                }
            } catch (err) {
                console.error(err);
                alert("Failed to delete case.");
            }
        }

        function editMember(memberId) {
            const m = state.members.find(item => item.id == memberId);
            if (!m) return;

            document.getElementById('memberFormId').value = m.id;
            document.getElementById('memberName').value = m.name;
            document.getElementById('memberPhone').value = m.phone ? m.phone : '';
            document.getElementById('memberAddress').value = m.address ? m.address : '';

            document.getElementById('memberModalTitle').textContent = "Edit Member Profile";
            memberModal.classList.add('active');
        }

        async function deleteMember(memberId) {
            if (!confirm("WARNING: Deleting this member will permanently delete ALL clinic cases linked to them. Are you sure you want to delete?")) {
                return;
            }

            try {
                const res = await fetch(`../api/delete_faith_clinic_member.php?id=${memberId}`);
                const r = await res.json();
                if (r.status === 'success') {
                    // Reset chosen selection folder
                    state.selectedPersonId = null;
                    await refreshDashboardData();
                } else {
                    alert("Delete failed: " + r.message);
                }
            } catch (err) {
                console.error(err);
                alert("Failed to delete member.");
            }
        }

        // HELPERS
        function escapeHTML(str) {
            if (!str) return '';
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>
