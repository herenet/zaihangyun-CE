<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>{{ $article->title }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="stylesheet" href="{{ asset('vendor/hxsen/editormd/editor.md/css/editormd.preview.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css') }}">
    <style>
        /* 基础样式 */
        body {
            margin: 0;
            padding: 0;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        .markdown-body {
            box-sizing: border-box;
            width: 100%;
            padding: 15px;
            max-width: 900px;
            margin: 0 auto;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* 通用表格样式 - 两种模式下保持一致的布局 */
        .markdown-body table {
            border-collapse: collapse !important;
            width: auto !important;
            max-width: 100% !important;
            margin-bottom: 16px !important;
            display: table !important; /* 强制使用表格布局 */
            table-layout: fixed !important; /* 固定表格布局 */
        }
        
        .markdown-body table th,
        .markdown-body table td {
            padding: 6px 13px !important;
            border-width: 1px !important;
            border-style: solid !important;
        }
        
        .markdown-body table tr {
            border-width: 1px 0 !important;
            border-style: solid !important;
        }
        
        .markdown-body table th {
            font-weight: 600 !important;
        }
        
        @media (max-width: 767px) {
            .markdown-body {
                padding: 15px;
            }
            
            /* 移动端表格样式调整 */
            .markdown-body table {
                display: block !important;
                overflow-x: auto !important;
            }
        }
        
        /* 明亮主题样式 */
        @if($theme === 'light')
        body {
            background-color: #ffffff;
        }
        
        .markdown-body {
            color: #24292e;
            background-color: #ffffff;
            box-shadow: none;
        }
        
        /* 明亮模式表格样式 */
        .markdown-body table {
            border-color: #dfe2e5 !important;
        }
        
        .markdown-body table th,
        .markdown-body table td {
            border-color: #dfe2e5 !important;
        }
        
        .markdown-body table tr {
            background-color: #ffffff !important;
            border-color: #dfe2e5 !important;
        }
        
        .markdown-body table tr:nth-child(2n) {
            background-color: #f6f8fa !important;
        }
        
        .markdown-body table th {
            background-color: #f6f8fa !important;
        }
        @endif
        
        /* 暗黑主题样式 */
        @if($theme === 'dark')
        body {
            background-color: #0d1117;
        }
        
        .markdown-body {
            color: #c9d1d9;
            background-color: #0d1117;
            box-shadow: none;
        }
        
        /* 暗黑模式表格样式 - 确保与明亮模式布局一致 */
        .markdown-body table {
            border-color: #30363d !important;
        }
        
        .markdown-body table th,
        .markdown-body table td {
            border-color: #30363d !important;
        }
        
        .markdown-body table tr {
            background-color: #0d1117 !important;
            border-color: #30363d !important;
        }
        
        .markdown-body table tr:nth-child(2n) {
            background-color: #161b22 !important;
        }
        
        .markdown-body table th {
            background-color: #161b22 !important;
            color: #58a6ff !important;
        }
        
        /* 标题样式 */
        .markdown-body h1 {
            color: #d2a8ff !important;
            border-bottom: 1px solid #30363d !important;
        }
        
        .markdown-body h2 {
            color: #79c0ff !important;
            border-bottom: 1px solid #30363d !important;
        }
        
        .markdown-body h3, .markdown-body h4 {
            color: #58a6ff !important;
        }
        
        .markdown-body h5, .markdown-body h6 {
            color: #8b949e !important;
        }
        
        /* 链接样式 */
        .markdown-body a {
            color: #58a6ff !important;
        }
        
        .markdown-body a:hover {
            color: #79c0ff !important;
            text-decoration: underline !important;
        }
        
        /* 强调和加粗 */
        .markdown-body strong {
            color: #ff7b72 !important;
        }
        
        .markdown-body em {
            color: #ffa657 !important;
            font-style: italic;
        }
        
        /* 代码块 */
        .markdown-body pre {
            background-color: #161b22 !important;
            border: 1px solid #30363d !important;
        }
        
        .markdown-body code {
            background-color: #161b22 !important;
            color: #c9d1d9 !important;
        }
        
        /* 行内代码 */
        .markdown-body p code, .markdown-body li code {
            background-color: #1f2937 !important;
            color: #ff7b72 !important;
            padding: 0.2em 0.4em !important;
            border-radius: 3px !important;
        }
        
        /* 引用块 */
        .markdown-body blockquote {
            border-left: 4px solid #3b5070 !important;
            color: #8b949e !important;
            background-color: #161b22 !important;
        }
        @endif
    </style>
</head>
<body>
    <div id="article-content" class="markdown-body editormd-html-preview">
        {!! $article->content !!}
    </div>
</body>
</html>