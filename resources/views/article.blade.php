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
    <style>
        .markdown-body {
            box-sizing: border-box;
            width: 100%;
        }
    </style>
</head>
<body style="margin: 0; padding: 0;">
    <div id="article-content" class="markdown-body editormd-html-preview">
        {!! $article->content !!}
    </div>  
</body>
</html>