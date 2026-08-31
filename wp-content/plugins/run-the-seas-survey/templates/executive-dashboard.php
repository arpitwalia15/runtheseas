<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Run The Seas - Executive Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Roboto, system-ui, sans-serif;
            background: #f0f2f5;
            color: #1e293b;
            padding: 16px;
        }
        .rts-dashboard {
            max-width: 1600px;
            margin: 0 auto;
        }

        .rts-header {
            background: linear-gradient(135deg, #0f172a 0%, #1a1a2e 100%);
            border-radius: 16px;
            padding: 20px 28px;
            margin-bottom: 24px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .rts-header h1 {
            font-size: 26px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .rts-header h1 i {
            color: #ffd700;
        }
        .rts-header .subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 400;
        }
        .rts-header-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .rts-header-actions button {
            padding: 8px 18px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .rts-header-actions button:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }
        .rts-header-actions .btn-primary {
            background: #1a7efb;
        }
        .rts-header-actions .btn-primary:hover {
            background: #1565c0;
        }
        .rts-header-actions .btn-success {
            background: #28a745;
        }
        .rts-header-actions .btn-success:hover {
            background: #1e7e34;
        }

        .rts-time-filter {
            background: #fff;
            border-radius: 14px;
            padding: 16px 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px 20px;
        }
        .rts-time-filter .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .rts-time-filter .btn-group {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
        .rts-time-filter .btn-group button {
            padding: 6px 16px;
            border: 1px solid #e2e8f0;
            background: #fff;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            cursor: pointer;
            transition: 0.2s;
        }
        .rts-time-filter .btn-group button:hover {
            background: #f1f5f9;
        }
        .rts-time-filter .btn-group button.active {
            background: #1a7efb;
            color: #fff;
            border-color: #1a7efb;
        }
        .rts-time-filter .date-inputs {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }
        .rts-time-filter .date-inputs input[type="date"] {
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
            background: #fff;
        }
        .rts-time-filter .date-inputs input[type="date"]:focus {
            border-color: #1a7efb;
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 126, 251, 0.1);
        }
        .rts-time-filter .apply-btn {
            padding: 6px 20px;
            background: #1a7efb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }
        .rts-time-filter .apply-btn:hover {
            background: #1565c0;
        }

        .rts-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
            margin-bottom: 24px;
        }
        .rts-stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 16px 18px 14px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            border-left: 4px solid #1a7efb;
            transition: 0.2s;
        }
        .rts-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .rts-stat-card .stat-icon {
            font-size: 18px;
            color: #1a7efb;
            opacity: 0.7;
        }
        .rts-stat-card .stat-number {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin: 2px 0 0;
        }
        .rts-stat-card .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #94a3b8;
            font-weight: 600;
        }
        .rts-stat-card.success { border-left-color: #28a745; }
        .rts-stat-card.success .stat-icon { color: #28a745; }
        .rts-stat-card.warning { border-left-color: #ffc107; }
        .rts-stat-card.warning .stat-icon { color: #ffc107; }
        .rts-stat-card.danger { border-left-color: #dc3545; }
        .rts-stat-card.danger .stat-icon { color: #dc3545; }
        .rts-stat-card.gold { border-left-color: #ffd700; }
        .rts-stat-card.gold .stat-icon { color: #ffd700; }
        .rts-stat-card.purple { border-left-color: #6f42c1; }
        .rts-stat-card.purple .stat-icon { color: #6f42c1; }

        .rts-live-stats {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .rts-live-box {
            background: #fff;
            border-radius: 14px;
            padding: 18px 22px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .rts-live-box .live-icon {
            font-size: 36px;
            color: #1a7efb;
        }
        .rts-live-box .live-icon.online { color: #28a745; }
        .rts-live-box .live-icon.trophy { color: #ffd700; }
        .rts-live-box .live-icon.country { color: #6f42c1; }
        .rts-live-box .live-number {
            font-size: 32px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        .rts-live-box .live-label {
            font-size: 14px;
            color: #64748b;
        }
        .rts-live-box .live-sub {
            font-size: 12px;
            color: #94a3b8;
        }

        .rts-chart-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .rts-chart-box {
            background: #fff;
            border-radius: 14px;
            padding: 18px 20px 14px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }
        .rts-chart-box h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .rts-chart-box canvas {
            max-height: 220px;
            max-width: 100%;
        }

        .rts-questions-section {
            background: #fff;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }
        .rts-questions-section h3 {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .rts-question-block {
            background: #f8fafc;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 10px;
            border-left: 4px solid #1a7efb;
        }
        .rts-question-block .q-title {
            font-weight: 600;
            font-size: 14px;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .rts-question-block .q-title .q-total {
            font-weight: 400;
            font-size: 12px;
            color: #94a3b8;
        }
        .rts-answer-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 2px 0;
        }
        .rts-answer-row .ans-label {
            min-width: 100px;
            font-size: 12px;
            color: #334155;
        }
        .rts-answer-row .bar-track {
            flex: 1;
            height: 14px;
            background: #e9edf2;
            border-radius: 20px;
            overflow: hidden;
        }
        .rts-answer-row .bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #1a7efb, #6c5ce7);
            border-radius: 20px;
            transition: width 0.6s ease;
        }
        .rts-answer-row .ans-pct {
            min-width: 40px;
            font-weight: 600;
            font-size: 12px;
            color: #0f172a;
            text-align: right;
        }
        .rts-answer-row .ans-votes {
            font-size: 11px;
            color: #94a3b8;
            min-width: 36px;
        }

        .rts-loading {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }
        .rts-loading .spinner {
            display: inline-block;
            width: 32px;
            height: 32px;
            border: 3px solid #e2e8f0;
            border-top-color: #1a7efb;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin-bottom: 12px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .rts-geo-list {
            max-height: 190px;
            overflow-y: auto;
            font-size: 13px;
            margin-top: 6px;
        }
        .rts-geo-list .geo-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .rts-geo-list .geo-item .geo-name {
            font-weight: 500;
            color: #1e293b;
        }
        .rts-geo-list .geo-item .geo-count {
            color: #64748b;
        }

        @media (max-width: 1024px) {
            .rts-chart-row { grid-template-columns: 1fr; }
            .rts-live-stats { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .rts-time-filter { flex-direction: column; align-items: stretch; }
            .rts-time-filter .date-inputs { margin-left: 0; flex-wrap: wrap; }
            .rts-stats-grid { grid-template-columns: 1fr 1fr; }
            .rts-header { flex-direction: column; align-items: stretch; text-align: center; }
            .rts-header-actions { justify-content: center; }
        }
        @media (max-width: 480px) {
            .rts-stats-grid { grid-template-columns: 1fr; }
            body { padding: 8px; }
        }

        @media print {
            body { background: #fff; padding: 0.3in; }
            .rts-time-filter, .rts-header-actions { display: none !important; }
            .rts-stat-card, .rts-chart-box, .rts-live-box, .rts-questions-section {
                box-shadow: none !important;
                border: 1px solid #e2e8f0;
            }
            .rts-header { background: #0f172a !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="rts-dashboard" id="rtsDashboard">

        <header class="rts-header">
            <div>
                <h1>
                    <i class="fas fa-crown"></i>
                    Executive Dashboard
                    <span class="subtitle">Run The Seas · Business Intelligence</span>
                </h1>
                <div style="font-size:13px;color:rgba(255,255,255,0.5);margin-top:2px;">
                    <span id="rtsLastUpdated">Loading data…</span>
                </div>
            </div>
            <div class="rts-header-actions">
                <button class="btn-primary" onclick="rtsExportReport()">
                    <i class="fas fa-file-pdf"></i> Export Report
                </button>
                <button class="btn-success" onclick="rtsExportCSV()">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button onclick="rtsRefresh()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </header>

        <div class="rts-time-filter" id="rtsTimeFilter">
            <span class="filter-label">
                <i class="fas fa-clipboard-list"></i> Active survey:
            </span>
            <select id="rtsExecutiveForm" onchange="rtsSetExecutiveForm(this.value)" aria-label="Active survey">
                <?php if (empty($rts_executive_forms)) : ?>
                    <option value="">No active surveys</option>
                <?php else : foreach ($rts_executive_forms as $form) : ?>
                    <option value="<?php echo esc_attr($form->id); ?>"><?php echo esc_html($form->title); ?></option>
                <?php endforeach; endif; ?>
            </select>
            <span class="filter-label">
                <i class="fas fa-calendar-alt"></i> Date Range:
            </span>
            <div class="btn-group" id="rtsTimeButtons">
                <button data-range="day" onclick="rtsSetTimeRange('day')">Day</button>
                <button data-range="week" onclick="rtsSetTimeRange('week')" class="active">Week</button>
                <button data-range="month" onclick="rtsSetTimeRange('month')">Month</button>
                <button data-range="quarter" onclick="rtsSetTimeRange('quarter')">Quarter</button>
                <button data-range="year" onclick="rtsSetTimeRange('year')">Year</button>
                <button data-range="custom" onclick="rtsSetTimeRange('custom')">Custom</button>
            </div>
            <div class="date-inputs" id="rtsDateInputs">
                <input type="date" id="rtsDateFrom" />
                <span style="color:#94a3b8;">→</span>
                <input type="date" id="rtsDateTo" />
            </div>
            <button class="apply-btn" onclick="rtsApplyFilters()">
                <i class="fas fa-filter"></i> Apply
            </button>
        </div>

        <div class="rts-stats-grid" id="rtsStatsGrid">
            <div class="rts-loading" style="grid-column:1/-1;padding:20px;">
                <div class="spinner"></div>
                Loading statistics…
            </div>
        </div>

        <div class="rts-live-stats" id="rtsLiveStats">
            <div class="rts-live-box">
                <div class="live-icon online">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div>
                    <div class="live-number" id="rtsOnlineUsers">—</div>
                    <div class="live-label">Currently Online</div>
                    <div class="live-sub">Logged-in users (unique)</div>
                </div>
            </div>
            <div class="rts-live-box">
                <div class="live-icon trophy">
                    <i class="fas fa-trophy"></i>
                </div>
                <div>
                    <div class="live-number" id="rtsTrophyVisits">—</div>
                    <div class="live-label">Trophy Room Visits</div>
                    <div class="live-sub">Total visits to trophy case</div>
                </div>
            </div>
            <div class="rts-live-box">
                <div class="live-icon country">
                    <i class="fas fa-globe-americas"></i>
                </div>
                <div>
                    <div class="live-number" id="rtsCountryCount">—</div>
                    <div class="live-label">Countries Reached</div>
                    <div class="live-sub">Unique countries in responses</div>
                </div>
            </div>
        </div>

        <div class="rts-chart-row">
            <div class="rts-chart-box">
                <h4><i class="fas fa-chart-line" style="color:#1a7efb;"></i> Survey Activity Trend</h4>
                <canvas id="rtsTrendChart"></canvas>
            </div>
            <div class="rts-chart-box">
                <h4><i class="fas fa-chart-pie" style="color:#6f42c1;"></i> Runner vs Non-Runner</h4>
                <canvas id="rtsRunnerChart"></canvas>
                <div style="margin-top:10px;font-size:12px;color:#94a3b8;text-align:center;">
                    Runners = Completed Surveys
                </div>
            </div>
        </div>

        <div class="rts-chart-row">
            <div class="rts-chart-box" style="grid-column: 1 / -1;">
                <h4><i class="fas fa-globe" style="color:#28a745;"></i> Geographic Distribution</h4>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <canvas id="rtsGeoChart"></canvas>
                    <div class="rts-geo-list" id="rtsGeoList">
                        <div style="color:#94a3b8;font-size:13px;">Loading geographic data…</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rts-questions-section">
            <h3><i class="fas fa-list-ul" style="color:#1a7efb;"></i> All Survey Questions Analytics</h3>
            <div id="rtsQuestionsContainer">
                <div class="rts-loading" style="padding:20px;">
                    <div class="spinner"></div>
                    Loading survey data…
                </div>
            </div>
        </div>

        <div style="text-align:center;font-size:12px;color:#94a3b8;padding:16px 0 8px;border-top:1px solid #e2e8f0;margin-top:8px;">
            Run The Seas &bull; Executive Dashboard &bull; Data updated in real-time
        </div>
    </div>

    <script>
        // ─────────────────────────────────────────────────────────────
        //  GLOBALS
        // ─────────────────────────────────────────────────────────────

        const rtsState = {
            formId: <?php echo !empty($rts_executive_forms) ? absint($rts_executive_forms[0]->id) : 0; ?>,
            range: 'week',
            dateFrom: '',
            dateTo: '',
            data: null,
            charts: {
                trend: null,
                runner: null,
                geo: null,
            },
            ajaxUrl: '<?php echo admin_url("admin-ajax.php"); ?>',
            nonce: '<?php echo wp_create_nonce("rts_admin_nonce"); ?>',
            refreshInterval: null,
        };

        // ─────────────────────────────────────────────────────────────
        //  INIT
        // ─────────────────────────────────────────────────────────────

        document.addEventListener('DOMContentLoaded', function() {
            rtsSetTimeRange('week');
            rtsApplyFilters();
            rtsState.refreshInterval = setInterval(() => {
                if (!document.hidden) {
                    rtsRefresh();
                }
            }, 60000);
        });

        // ─────────────────────────────────────────────────────────────
        //  TIME RANGE FUNCTIONS
        // ─────────────────────────────────────────────────────────────

        function rtsSetTimeRange(range) {
            rtsState.range = range;
            document.querySelectorAll('#rtsTimeButtons button').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.range === range);
            });

            const now = new Date();
            const from = new Date();

            switch (range) {
                case 'day': from.setDate(now.getDate() - 1); break;
                case 'week': from.setDate(now.getDate() - 7); break;
                case 'month': from.setMonth(now.getMonth() - 1); break;
                case 'quarter': from.setMonth(now.getMonth() - 3); break;
                case 'year': from.setFullYear(now.getFullYear() - 1); break;
                case 'custom': return;
                default: from.setDate(now.getDate() - 7);
            }

            document.getElementById('rtsDateFrom').value = from.toISOString().split('T')[0];
            document.getElementById('rtsDateTo').value = now.toISOString().split('T')[0];

            if (range !== 'custom') {
                rtsApplyFilters();
            }
        }

        // ─────────────────────────────────────────────────────────────
        //  FETCH DATA
        // ─────────────────────────────────────────────────────────────

        function rtsApplyFilters() {
            if (!rtsState.formId) {
                document.getElementById('rtsStatsGrid').innerHTML = '<div class="rts-loading" style="grid-column:1/-1;padding:20px;">Activate a survey in Survey Management before viewing BI data.</div>';
                return;
            }
            rtsState.dateFrom = document.getElementById('rtsDateFrom').value;
            rtsState.dateTo = document.getElementById('rtsDateTo').value;

            document.getElementById('rtsStatsGrid').innerHTML =
                '<div class="rts-loading" style="grid-column:1/-1;padding:20px;"><div class="spinner"></div>Loading statistics…</div>';
            document.getElementById('rtsQuestionsContainer').innerHTML =
                '<div class="rts-loading" style="padding:20px;"><div class="spinner"></div>Loading data…</div>';
            document.getElementById('rtsGeoList').innerHTML =
                '<div style="color:#94a3b8;font-size:13px;">Loading geographic data…</div>';

            const payload = new URLSearchParams({
                action: 'rts_get_analytics_data',
                form_id: rtsState.formId,
                date_from: rtsState.dateFrom,
                date_to: rtsState.dateTo,
                nonce: rtsState.nonce,
            });

            fetch(rtsState.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: payload,
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success && response.data) {
                        rtsState.data = response.data;
                        rtsRenderDashboard(response.data);
                        document.getElementById('rtsLastUpdated').textContent =
                            'Last updated: ' + new Date().toLocaleString();
                    } else {
                        rtsShowError('Failed to load data: ' + (response.data || 'Unknown error'));
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    rtsShowError('Network error. Please try again.');
                });

            rtsFetchLiveStats();
        }

        function rtsSetExecutiveForm(formId) {
            rtsState.formId = parseInt(formId, 10) || 0;
            rtsApplyFilters();
        }

        function rtsFetchLiveStats() {
            // Get only logged-in users (not sessions/tabs)
            fetch(rtsState.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'rts_get_logged_in_users',
                        nonce: rtsState.nonce,
                    }),
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        document.getElementById('rtsOnlineUsers').textContent = response.data.count || 0;
                    }
                })
                .catch(() => {
                    document.getElementById('rtsOnlineUsers').textContent = '—';
                });

            // Trophy room visits
            fetch(rtsState.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'rts_get_trophy_visits',
                        nonce: rtsState.nonce,
                        date_from: rtsState.dateFrom,
                        date_to: rtsState.dateTo,
                    }),
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        document.getElementById('rtsTrophyVisits').textContent = response.data.count || 0;
                    }
                })
                .catch(() => {
                    document.getElementById('rtsTrophyVisits').textContent = '—';
                });
        }

        function rtsShowError(msg) {
            document.getElementById('rtsStatsGrid').innerHTML =
                '<div style="grid-column:1/-1;text-align:center;padding:30px;color:#dc3545;background:#f8d7da;border-radius:12px;">' +
                '<i class="fas fa-exclamation-triangle" style="font-size:28px;display:block;margin-bottom:8px;"></i>' +
                msg +
                '</div>';
        }

        // ─────────────────────────────────────────────────────────────
        //  RENDER DASHBOARD
        // ─────────────────────────────────────────────────────────────

        function rtsRenderDashboard(data) {
            rtsRenderStats(data.stats);
            rtsRenderTrendChart(data.trends);
            rtsRenderRunnerChart(data.stats);
            rtsRenderGeoChart(data.geo);
            rtsRenderQuestions(data.questions);
            
            // Update country count
            if (data.geo && data.geo.labels) {
                document.getElementById('rtsCountryCount').textContent = data.geo.labels.length || 0;
            }
        }

        // ─── STATS ───

        function rtsRenderStats(stats) {
            if (!stats) {
                document.getElementById('rtsStatsGrid').innerHTML =
                    '<div style="grid-column:1/-1;text-align:center;padding:20px;color:#94a3b8;">No data available</div>';
                return;
            }

            const foundingRunners = Math.min(
                stats.completed || 0,
                stats.verified_emails || 0,
                stats.unique_respondents || 0
            );

            const items = [
                { key: 'total_responses', label: 'Total Responses', icon: 'fa-file-signature', cls: '', val: stats
                        .total_responses || 0 },
                { key: 'completed', label: 'Completed Surveys', icon: 'fa-check-circle', cls: 'success', val: stats
                        .completed || 0 },
                { key: 'verified_emails', label: 'Verified Participants', icon: 'fa-envelope', cls: 'success', val: stats
                        .verified_emails || 0 },
                { key: 'founding_runners', label: '🏆 Founding Runners', icon: 'fa-crown', cls: 'gold', val: foundingRunners },
                { key: 'completion_percentage', label: 'Completion Rate', icon: 'fa-percent', cls: 'success', val: (stats
                        .completion_percentage || 0) + '%' },
                { key: 'abandonment_rate', label: 'Abandonment Rate', icon: 'fa-arrow-trend-down', cls: 'warning', val: (stats
                        .abandonment_rate || 0) + '%' },
                { key: 'avg_completion_time', label: 'Avg Time', icon: 'fa-clock', cls: 'info', val: rtsFormatTime(stats
                        .avg_completion_time) },
                { key: 'referral_participation', label: 'Referral Participation', icon: 'fa-link', cls: 'purple', val: stats
                        .referral_participation || 0 },
                { key: 'cabin_credits_issued', label: 'Cabin Credits', icon: 'fa-ticket', cls: 'gold', val: stats
                        .cabin_credits_issued || 0 },
                { key: 'unique_respondents', label: 'Unique Users', icon: 'fa-user', cls: 'info', val: stats
                        .unique_respondents || 0 },
            ];

            let html = '';
            items.forEach(item => {
                html += `
                        <div class="rts-stat-card ${item.cls}">
                            <div class="stat-icon"><i class="fas ${item.icon}"></i></div>
                            <div class="stat-number">${item.val}</div>
                            <div class="stat-label">${item.label}</div>
                        </div>
                    `;
            });

            document.getElementById('rtsStatsGrid').innerHTML = html;
        }

        function rtsFormatTime(seconds) {
            if (!seconds || seconds < 0) return '0s';
            if (seconds < 60) return Math.round(seconds) + 's';
            if (seconds < 3600) return Math.floor(seconds / 60) + 'm ' + Math.round(seconds % 60) + 's';
            return Math.floor(seconds / 3600) + 'h ' + Math.floor((seconds % 3600) / 60) + 'm';
        }

        // ─── TREND CHART ───

        function rtsRenderTrendChart(trends) {
            const canvas = document.getElementById('rtsTrendChart');
            const ctx = canvas.getContext('2d');

            if (rtsState.charts.trend) {
                rtsState.charts.trend.destroy();
                rtsState.charts.trend = null;
            }

            const labels = trends?.labels || ['No Data'];
            const started = trends?.started || [0];
            const completed = trends?.completed || [0];

            rtsState.charts.trend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Started',
                        data: started,
                        borderColor: '#1a7efb',
                        backgroundColor: 'rgba(26,126,251,0.08)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 2,
                        borderWidth: 2,
                    }, {
                        label: 'Completed',
                        data: completed,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40,167,69,0.08)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 2,
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { boxWidth: 12, padding: 10, font: { size: 11 } }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { font: { size: 10 } } },
                        x: { ticks: { font: { size: 9 }, maxRotation: 45, autoSkip: true } }
                    }
                }
            });
        }

        // ─── RUNNER VS NON-RUNNER CHART ───

        function rtsRenderRunnerChart(stats) {
            const canvas = document.getElementById('rtsRunnerChart');
            const ctx = canvas.getContext('2d');

            if (rtsState.charts.runner) {
                rtsState.charts.runner.destroy();
                rtsState.charts.runner = null;
            }

            const total = stats?.total_responses || 0;
            const runners = stats?.completed || 0;
            const nonRunners = Math.max(0, total - runners);

            rtsState.charts.runner = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Runners (Completed)', 'Non-Runners'],
                    datasets: [{
                        data: [runners, nonRunners],
                        backgroundColor: ['#28a745', '#e2e8f0'],
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, padding: 10, font: { size: 11 } }
                        }
                    },
                    cutout: '55%',
                }
            });
        }

        // ─── GEO CHART ───

        function rtsRenderGeoChart(geo) {
            const canvas = document.getElementById('rtsGeoChart');
            const ctx = canvas.getContext('2d');

            if (rtsState.charts.geo) {
                rtsState.charts.geo.destroy();
                rtsState.charts.geo = null;
            }

            const labels = geo?.labels || ['No Data'];
            const counts = geo?.counts || [1];
            const colors = ['#1a7efb', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997',
                '#e83e8c'
            ];

            rtsState.charts.geo = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: colors.slice(0, labels.length),
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, padding: 8, font: { size: 9 } }
                        }
                    },
                    cutout: '50%',
                }
            });

            // Geo list
            const listContainer = document.getElementById('rtsGeoList');
            if (geo?.labels?.length) {
                let listHtml = '';
                const total = counts.reduce((a, b) => a + b, 0);
                geo.labels.forEach((label, i) => {
                    const count = counts[i] || 0;
                    const pct = total > 0 ? ((count / total) * 100).toFixed(0) : 0;
                    listHtml += `
                            <div class="geo-item">
                                <span class="geo-name">${label}</span>
                                <span class="geo-count">${count} (${pct}%)</span>
                            </div>
                        `;
                });
                listContainer.innerHTML = listHtml;
            } else {
                listContainer.innerHTML = '<div style="color:#94a3b8;font-size:13px;">No geographic data available</div>';
            }
        }

        // ─── QUESTIONS (ALL QUESTIONS) ───

        function rtsRenderQuestions(questions) {
            const container = document.getElementById('rtsQuestionsContainer');

            if (!questions || !questions.length) {
                container.innerHTML =
                    '<div style="color:#94a3b8;padding:20px;text-align:center;">No survey data available yet.</div>';
                return;
            }

            let html = '';
            // Show ALL questions
            questions.forEach(q => {
                const total = q.total_votes || 0;
                html += `
                        <div class="rts-question-block">
                            <div class="q-title">
                                ${q.question_label || q.question_id}
                                <span class="q-total">(${total} responses)</span>
                            </div>
                    `;

                if (q.answers && q.answers.length) {
                    // Show ALL answers for each question
                    q.answers.forEach(ans => {
                        const pct = typeof ans.percentage === 'number' ? ans.percentage : 0;
                        const votes = ans.total_votes || 0;
                        html += `
                                <div class="rts-answer-row">
                                    <span class="ans-label">${ans.answer_option || 'Other'}</span>
                                    <div class="bar-track">
                                        <div class="bar-fill" style="width:${Math.min(pct, 100)}%;"></div>
                                    </div>
                                    <span class="ans-pct">${pct.toFixed(0)}%</span>
                                    <span class="ans-votes">(${votes})</span>
                                </div>
                            `;
                    });
                } else {
                    html +=
                        '<div style="color:#94a3b8;font-size:13px;padding:4px 0;">No responses for this question</div>';
                }

                html += '</div>';
            });

            container.innerHTML = html;
        }

        // ─────────────────────────────────────────────────────────────
        //  EXPORT FUNCTIONS
        // ─────────────────────────────────────────────────────────────

        function rtsExportReport() {
            alert('📊 Professional Executive Report\n\n' +
                'This will generate a comprehensive report including:\n' +
                '• Executive Summary\n' +
                '• Key Performance Indicators\n' +
                '• All Survey Questions Analysis\n' +
                '• Geographic Distribution\n' +
                '• Runner vs Non-Runner Analysis\n' +
                '• Referral Metrics\n' +
                '• Growth Trends\n\n' +
                'The report will be formatted for executives, investors, and cruise line partners.');
            window.print();
        }

        function rtsExportCSV() {
            if (!rtsState.data) {
                alert('No data to export. Please load data first.');
                return;
            }

            const url = rtsState.ajaxUrl +
                '?action=rts_export_analytics&form_id=' + rtsState.formId +
                '&nonce=' + rtsState.nonce +
                '&date_from=' + encodeURIComponent(rtsState.dateFrom) +
                '&date_to=' + encodeURIComponent(rtsState.dateTo) +
                '&format=csv';
            window.open(url, '_blank');
        }

        function rtsRefresh() {
            if (rtsState.dateFrom && rtsState.dateTo) {
                rtsApplyFilters();
            } else {
                rtsSetTimeRange(rtsState.range || 'week');
            }
        }

        // ─────────────────────────────────────────────────────────────
        //  EXPOSE GLOBALLY
        // ─────────────────────────────────────────────────────────────

        window.rtsSetTimeRange = rtsSetTimeRange;
        window.rtsApplyFilters = rtsApplyFilters;
        window.rtsSetExecutiveForm = rtsSetExecutiveForm;
        window.rtsExportReport = rtsExportReport;
        window.rtsExportCSV = rtsExportCSV;
        window.rtsRefresh = rtsRefresh;

        // ─────────────────────────────────────────────────────────────
        //  RESIZE HANDLER
        // ─────────────────────────────────────────────────────────────

        window.addEventListener('resize', function() {
            // Charts auto-resize
        });
    </script>

</body>
</html>
