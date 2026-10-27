<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — {{ isset($post) ? 'Yazı Düzenle' : 'Yeni Yazı' }}</title>
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

        /* CKEditor — light theme */
        .blog-editor-wrap {
            --ck-color-base-background: #ffffff;
            --ck-color-base-foreground: #111827;
            --ck-color-base-border: #e5e7eb;
            --ck-color-base-text: #111827;
            --ck-color-base-active: #f3b726;
            --ck-color-base-active-focus: #ffc107;
            --ck-color-base-error: #dc2626;
            --ck-color-toolbar-background: #f9fafb;
            --ck-color-toolbar-border: #e5e7eb;
            --ck-color-button-default-background: transparent;
            --ck-color-button-default-hover-background: #f3f4f6;
            --ck-color-button-default-active-background: #e5e7eb;
            --ck-color-button-on-background: rgba(255, 193, 7, 0.18);
            --ck-color-button-on-color: #92400e;
            --ck-color-button-on-hover-background: rgba(255, 193, 7, 0.28);
            --ck-color-list-background: #ffffff;
            --ck-color-list-button-hover-background: #f3f4f6;
            --ck-color-list-button-on-background: rgba(255, 193, 7, 0.15);
            --ck-color-list-button-on-text: #92400e;
            --ck-color-dropdown-panel-background: #ffffff;
            --ck-color-dropdown-panel-border: #e5e7eb;
            --ck-color-input-background: #ffffff;
            --ck-color-input-border: #d1d5db;
            --ck-color-input-text: #111827;
            --ck-color-input-disabled-background: #f9fafb;
            --ck-color-input-disabled-border: #e5e7eb;
            --ck-color-input-disabled-text: #9ca3af;
            --ck-color-focus-border: #f59e0b;
            --ck-color-focus-outer-shadow: rgba(245, 158, 11, 0.2);
            --ck-color-shadow-drop: rgba(0, 0, 0, 0.12);
            --ck-color-shadow-inner: rgba(0, 0, 0, 0.06);
            --ck-border-radius: 8px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            background: #ffffff;
        }

        .blog-editor-wrap .ck-editor__editable {
            min-height: 360px;
            max-height: 560px;
        }

        .blog-editor-wrap .ck.ck-editor__editable:not(.ck-editor__nested-editable) {
            background: #ffffff;
            color: #111827;
            border-color: #e5e7eb;
        }

        .blog-editor-wrap .ck.ck-editor__editable:not(.ck-editor__nested-editable).ck-focused {
            border-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.15);
        }

        .blog-editor-wrap .ck.ck-toolbar {
            background: #f9fafb;
            border-color: #e5e7eb;
        }

        .blog-editor-wrap .ck.ck-toolbar .ck-toolbar__separator {
            background: #e5e7eb;
        }

        .blog-editor-wrap .ck.ck-button .ck-button__label,
        .blog-editor-wrap .ck.ck-dropdown .ck-button .ck-button__label {
            color: #374151;
        }

        .blog-editor-wrap .ck.ck-icon {
            color: #374151;
        }

        .blog-editor-wrap .ck.ck-editor__main>.ck-editor__editable {
            background: #ffffff;
        }

        .blog-editor-wrap .ck.ck-editor__top .ck-sticky-panel .ck-toolbar {
            border-radius: 8px 8px 0 0;
        }

        .blog-editor-wrap .ck.ck-balloon-panel {
            background: #ffffff;
            border-color: #e5e7eb;
        }

        .blog-editor-error {
            margin-bottom: 0.75rem;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #fca5a5;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        .blog-editor-wrap textarea {
            display: none;
        }

        .blog-featured-image-field {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .blog-featured-image-preview-wrap {
            position: relative;
            display: inline-block;
            max-width: 320px;
        }

        .blog-featured-image-preview-wrap[hidden] {
            display: none !important;
        }

        .blog-featured-image-preview {
            display: block;
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--border);
        }

        .blog-featured-image-remove {
            position: absolute;
            top: 8px;
            right: 8px;
            border: none;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #fff;
            background: rgba(0, 0, 0, 0.72);
            cursor: pointer;
        }

        .blog-featured-image-upload {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            width: fit-content;
            padding: 0.65rem 0.9rem;
            border-radius: 8px;
            border: 1px dashed var(--border);
            color: var(--muted);
            cursor: pointer;
        }

        .blog-featured-image-upload input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
</head>

<body cz-shortcut-listen="true">
    <div id="root">
        <div class="admin-root">
            @include('Admin.layouts.header')
            <main class="admin-main">
                @php
                    $isEdit = isset($post);
                    $formAction = $isEdit ? route('admin.blogs.update', $post->id) : route('admin.blogs.store');
                    $currentStatus = old('status', $post->status ?? 'draft');
                @endphp
                <div style="max-width: 860px; margin: 0px auto;">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div>
                            <h1 style="margin: 0px; font-size: 1.3rem; font-weight: 700;">
                                {{ $isEdit ? 'Yazı Düzenle' : 'Yeni Yazı' }}</h1>
                            <p style="margin: 4px 0px 0px; color: var(--muted); font-size: 0.82rem;">CKEditor ile zengin
                                metin — başlıklar, listeler, bağlantılar ve görseller</p>
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <a class="btn ghost" href="{{ route('admin.blogs.list') }}">İptal</a>
                            <button type="submit" form="blog-form" name="status" value="draft"
                                class="btn ghost">Taslak Kaydet</button>
                            <button type="submit" form="blog-form" name="status" value="published"
                                class="btn">Yayınla</button>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="blog-editor-error" style="margin-bottom: 1rem;">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form id="blog-form" method="POST" action="{{ $formAction }}" enctype="multipart/form-data"
                        style="display: flex; flex-direction: column; gap: 1rem;">
                        @csrf
                        @if ($isEdit)
                            @method('PUT')
                        @endif
                        <div class="card" style="padding: 1rem 1.25rem;">
                            <label for="title"
                                style="display: block; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); margin-bottom: 0.4rem;">Başlık
                                *</label>
                            <input id="title" name="title" required placeholder="Harika blog yazımın başlığı"
                                value="{{ old('title', $post->title ?? '') }}"
                                style="width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; padding: 0.6rem 0.8rem; color: var(--text); font-size: 0.95rem; outline: none;">
                        </div>
                        <div class="card" style="padding: 1rem 1.25rem;">
                            <label for="slug"
                                style="display: block; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); margin-bottom: 0.4rem;">Slug
                                *</label>
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <span style="color: var(--muted); font-size: 0.88rem;">/blog/</span>
                                <input id="slug" name="slug" required placeholder="my-post-slug"
                                    value="{{ old('slug', $post->slug ?? '') }}"
                                    style="width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; padding: 0.6rem 0.8rem; color: var(--text); font-size: 0.95rem; outline: none; flex: 1 1 0%;">
                            </div>
                        </div>
                        <div class="card" style="padding: 1rem 1.25rem;">
                            <label for="excerpt"
                                style="display: block; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); margin-bottom: 0.4rem;">Özet
                                <span style="color: var(--muted);">(listelemede görünür)</span>
                            </label>
                            <textarea id="excerpt" name="excerpt" placeholder="Bu yazı hakkında kısa açıklama..."
                                style="width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; padding: 0.6rem 0.8rem; color: var(--text); font-size: 0.95rem; outline: none; height: 72px; resize: vertical;">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                        </div>
                        <div class="card blog-featured-image-field" style="padding: 1rem 1.25rem;">
                            <label for="featured_image"
                                style="display: block; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); margin-bottom: 0.4rem;">Blog
                                Görseli <span style="font-weight: 500; text-transform: none;">(liste ve detay sayfasında görünür)</span></label>
                            <p style="margin: 0 0 0.5rem; color: var(--muted); font-size: 0.78rem;">JPG, PNG, GIF veya WebP — en fazla 4 MB.</p>

                            @php
                                $currentFeaturedImage = $isEdit ? $post->featuredImageUrl() : null;
                            @endphp

                            <div class="blog-featured-image-preview-wrap" id="blog-featured-image-preview-wrap"
                                @if (! $currentFeaturedImage) hidden @endif>
                                <img id="blog-featured-image-preview" class="blog-featured-image-preview"
                                    alt="Blog görseli önizleme"
                                    @if ($currentFeaturedImage) src="{{ $currentFeaturedImage }}" @endif>
                                <button type="button" class="blog-featured-image-remove" id="blog-featured-image-remove"
                                    aria-label="Görseli kaldır">Kaldır</button>
                            </div>

                            <label class="blog-featured-image-upload" style="position: relative;">
                                Görsel seç
                                <input id="featured_image" name="featured_image" type="file" accept="image/*">
                            </label>

                            @if ($isEdit)
                                <input type="hidden" name="remove_featured_image" id="remove_featured_image"
                                    value="0">
                            @endif

                            @error('featured_image')
                                <p style="margin: 0; color: #fca5a5; font-size: 0.82rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="card" style="padding: 1rem 1.25rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <label for="blog-content"
                                    style="display: block; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); margin-bottom: 0.4rem;">İçerik
                                    *</label>
                                <span id="blog-content-char-count" style="font-size: 0.75rem; color: var(--muted);">0
                                    karakter</span>
                            </div>
                            <div id="blog-editor-error" class="blog-editor-error" hidden></div>
                            <div class="blog-editor-wrap">
                                <textarea id="blog-content" name="content" required>{!! old('content', $post->content ?? '') !!}</textarea>
                            </div>
                        </div>
                        <div class="card"
                            style="padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; font-size: 0.88rem;">Durum</div>
                                <div style="color: var(--muted); font-size: 0.78rem; margin-top: 2px;">Taslak yazılar
                                    sadece adminler tarafından görülebilir</div>
                            </div>
                            @if ($currentStatus === 'published')
                                <span
                                    style="padding: 4px 14px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; background: rgba(34, 197, 94, 0.15); color: rgb(74, 222, 128);">yayında</span>
                            @else
                                <span
                                    style="padding: 4px 14px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; background: rgba(255, 255, 255, 0.06); color: var(--muted);">taslak</span>
                            @endif
                        </div>
                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding-bottom: 2rem;">
                            <a class="btn ghost" href="{{ route('admin.blogs.list') }}">İptal</a>
                            <button type="submit" name="status" value="draft" class="btn ghost">Taslak
                                Kaydet</button>
                            <button type="submit" name="status" value="published" class="btn">Yayınla</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="{{ asset('assets/vendor/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/vendor/ckeditor/tr.js') }}"></script>
    <script src="{{ asset('assets/js/admin-blog-editor.js') }}"></script>
</body>

</html>
