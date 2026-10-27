<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Denetim</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <style type="text/css">
        :root {
            color-scheme: dark;
            --bg: #0b0c14;
            --panel: #141529;
            --border: rgba(255, 255, 255, 0.08);
            --text: #e5e7eb;
            --muted: #9ca3af;
            --accent: #FFC107;
            --danger: #f87171;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
        }

        #root {
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .admin-root {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-top {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            background: rgba(20, 21, 41, 0.95);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 10;
            flex-wrap: wrap;
        }

        .brand {
            font-weight: 700;
            letter-spacing: 0.04em;
            flex-shrink: 0;
        }

        /* Nav: yatay scroll on small screens, flex-wrap on wider */
        .admin-nav {
            display: flex;
            gap: 0.25rem;
            flex: 1;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            margin: 0 -0.5rem;
            padding: 0 0.5rem;
        }

        .admin-nav::-webkit-scrollbar {
            display: none;
        }

        .nav-link {
            padding: 0.6rem 0.85rem;
            border-radius: 0.5rem;
            color: var(--muted);
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            font-size: 0.92rem;
        }

        .nav-link.active {
            color: var(--accent);
            background: rgba(243, 183, 36, 0.12);
        }

        .admin-main {
            flex: 1;
            padding: 1rem;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .admin-top {
                padding: 0.75rem 1.25rem;
                gap: 1rem;
            }

            .admin-main {
                padding: 1.5rem;
            }
        }

        .admin-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            gap: 1rem;
        }

        .admin-shell.narrow {
            max-width: 420px;
            margin: 0 auto;
        }

        .stack {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .gap-lg {
            gap: 1.25rem;
        }

        .row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1rem;
        }

        @media (min-width: 768px) {
            .card {
                padding: 1.25rem 1.5rem;
            }
        }

        .chart-card h2 {
            margin-top: 0;
        }

        .stat {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.25rem 0 0;
        }

        .muted {
            color: var(--muted);
            margin: 0;
        }

        .error {
            color: var(--danger);
            margin: 0;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            font-size: 0.9rem;
        }

        .field input,
        .field textarea,
        .input,
        .select {
            border-radius: 0.65rem;
            border: 1px solid var(--border);
            background: #0f1018;
            color: var(--text);
            padding: 0.65rem 0.75rem;
            font: inherit;
        }

        .btn {
            border-radius: 0.65rem;
            border: 1px solid var(--border);
            background: #1b1c2a;
            color: var(--text);
            padding: 0.55rem 0.9rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn.primary {
            background: linear-gradient(135deg, #FFC107, #FF9800);
            color: #111827;
            border: none;
        }

        .btn.ghost {
            background: transparent;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Tablo container — responsive scroll. Bütün <table> elemanlarını otomatik
   * sarmaladığında soldan/sağdan kayma şipşak kayıyor; mobil ekranlarda
   * tablo daralmıyor, scroll-x veriyor. */
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        @media (min-width: 768px) {
            .table {
                display: table;
                white-space: normal;
            }
        }

        .table th,
        .table td {
            text-align: left;
            padding: 0.65rem 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            color: var(--muted);
            font-weight: 600;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* Buttons: touch-friendly heights */
        .btn {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }


        h1,
        h2 {
            margin: 0 0 0.5rem;
        }

        h1 {
            font-size: 1.5rem;
        }

        h2 {
            font-size: 1.1rem;
        }
    </style>
</head>

<body cz-shortcut-listen="true">
    <div id="root">
        <div class="admin-root">
            @include('Admin.layouts.header')
            <main class="admin-main">
                <div class="stack gap-lg">
                    <div class="row-between">
                        <h1>Denetim Günlüğü</h1><select class="select">
                            <option value="">Tüm işlemler</option>
                            <option value="submission.">Başvurular</option>
                            <option value="submission.approve">Sadece Onaylar</option>
                            <option value="submission.reject">Sadece Redler</option>
                        </select>
                    </div>
                    <div class="card">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Kim</th>
                                    <th>İşlem</th>
                                    <th>Hedef</th>
                                    <th>Detaylar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td title="2026-05-14T15:34:48.31843+00:00">14.05.2026 18:34:48</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "atalanta-dental-arts",
  "business_id": "475fd6ce-5ed7-4f70-b29a-9f93a3a8d0ad",
  "business_name": "Atalanta Dental Arts"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T15:34:46.803377+00:00">14.05.2026 18:34:46</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "cake-wallet",
  "business_id": "77003807-f1e6-4ba9-9504-4a26b7aa1e4a",
  "business_name": "Cake Wallet"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T15:32:00.749434+00:00">14.05.2026 18:32:00</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.reject</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "business_name": "Atalanta Dental Arts",
  "reviewer_note": null
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T15:11:18.528839+00:00">14.05.2026 18:11:18</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "atalanta-dental-arts",
  "business_id": "4d5ea3de-08c2-43a4-ad82-9c4710acd7e3",
  "business_name": "Atalanta Dental Arts"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T15:11:16.854394+00:00">14.05.2026 18:11:16</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "apex-business-partners-578bf",
  "business_id": "77e44858-e3fe-4df4-969e-3004d891da47",
  "business_name": "Apex Business Partners"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T15:11:14.936137+00:00">14.05.2026 18:11:14</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "burrell-law-firm-nyc",
  "business_id": "c031ac64-abb9-4c40-846b-2ed9bbdf560c",
  "business_name": "Burrell Law Firm NYC"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T15:11:13.262758+00:00">14.05.2026 18:11:13</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "fibeecups",
  "business_id": "efc10c48-dc03-40ec-82ab-275a884f1009",
  "business_name": "FibeeCups"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T15:11:11.630289+00:00">14.05.2026 18:11:11</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "hivelocity",
  "business_id": "a34b5d1c-5d68-4b41-8c79-1bdbc888cf37",
  "business_name": "Hivelocity"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T15:11:10.12768+00:00">14.05.2026 18:11:10</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "apex-business-partners",
  "business_id": "12ae1ae5-89ba-4ec1-a075-6c2173bd07fa",
  "business_name": "Apex Business Partners"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T15:11:08.610345+00:00">14.05.2026 18:11:08</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "widevpn",
  "business_id": "add9de6f-2483-4946-82d0-acc311ab739e",
  "business_name": "Widevpn"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T14:35:48.72252+00:00">14.05.2026 17:35:48</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "karin-beauty-studio",
  "business_id": "4a64ee79-4088-463e-b51b-2ced5b939174",
  "business_name": "Karin Beauty Studio"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T14:03:48.796236+00:00">14.05.2026 17:03:48</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "dirty-daves",
  "business_id": "955b3704-03c5-4419-a1c3-38fb88291349",
  "business_name": "DIRTY DAVES"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-14T13:55:11.24164+00:00">14.05.2026 16:55:11</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "zbase",
  "business_id": "0bdd3447-14fd-4a3a-a418-8b8a4e24abe5",
  "business_name": "Zbase"
}</pre>
                                        </details>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="2026-05-11T12:36:31.143596+00:00">11.05.2026 15:36:31</td>
                                    <td>admin</td>
                                    <td><code style="font-size: 0.85em;">submission.approve</code></td>
                                    <td><span class="muted">submission</span></td>
                                    <td>
                                        <details>
                                            <summary class="muted" style="cursor: pointer;">gör</summary>
                                            <pre style="font-size: 0.78em; margin-top: 8px; max-width: 400px; white-space: pre-wrap;">{
  "slug": "cryptobooks-lisbon",
  "business_id": "2fa5ed5c-8638-41e6-a75e-b4f60f06a561",
  "business_name": "CryptoBooks Lisbon"
}
</pre>
                                        </details>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="muted" style="font-size: 0.85em;">Son 500 olay gösteriliyor. Yetkili işlemler (örn.
                        onay/red) veritabanı tarafından otomatik olarak kaydedilir.</p>
                </div>
            </main>
        </div>
    </div>
    <script type="module" src="/src/main.tsx"></script>


    <div id="extension-dpfof"></div>
</body>

</html>
