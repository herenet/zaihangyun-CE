@extends('layouts.article_cate')

@section('title', $category->name ?? '文章分类')

@section('styles')
<style>
    /* 基础样式，不受主题影响的部分 */
    .category-container {
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .article-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .article-item {
        padding: 10px 0;
        position: relative;
        transition: all 0.2s ease;
    }
    
    .article-item:last-child {
        border-bottom: none;
    }
    
    .article-link {
        font-size: 16px;
        text-decoration: none;
        display: block;
        padding: 4px 0;
        position: relative;
        transition: all 0.2s ease;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 0;
        transition: all 0.3s ease;
    }
    
    .empty-state i {
        font-size: 56px;
        margin-bottom: 20px;
        display: block;
        transition: all 0.3s ease;
    }
    
    .empty-state p {
        font-size: 16px;
        margin-bottom: 0;
        opacity: 0.8;
    }
    
    .loading {
        text-align: center;
        padding: 20px;
        display: none;
        transition: all 0.3s ease;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        animation: spin 1s ease-in-out infinite;
        transition: all 0.3s ease;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    @media (max-width: 767.98px) {
        .category-container {
            padding: 15px;
            border-radius: 0;
            box-shadow: none !important;
        }
        
        .article-link {
            font-size: 15px;
        }
        
        .article-item {
            padding: 8px 0;
        }
    }
    
    /* 明亮主题的样式 - 更精致现代 */
    @if($theme === 'light')
    body {
        background-color: #f7f9fc;
        color: #333333;
    }
    
    .category-container {
        background-color: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.04);
    }
    
    .article-item {
        border-bottom: 1px solid #edf2f7;
    }
    
    .article-item:hover {
        background-color: rgba(0, 0, 0, 0.01);
        transform: translateX(5px);
    }
    
    .article-link {
        color: #2d3748;
    }
    
    .article-link:hover {
        color: #3182ce;
    }
    
    .empty-state {
        color: #a0aec0;
    }
    
    .loading {
        color: #a0aec0;
    }
    
    .loading-spinner {
        border: 2px solid rgba(66, 153, 225, 0.3);
        border-top-color: #4299e1;
    }
    @endif
    
    /* 暗黑主题的样式 - 更精致现代 */
    @if($theme === 'dark')
    body {
        background-color: #171923;
        color: #e2e8f0;
    }
    
    .category-container {
        background-color: #1a202c;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .article-item {
        border-bottom: 1px solid #2d3748;
    }
    
    .article-item:hover {
        background-color: rgba(255, 255, 255, 0.03);
        transform: translateX(5px);
    }
    
    .article-link {
        color: #e2e8f0;
    }
    
    .article-link:hover {
        color: #90cdf4;
    }
    
    .empty-state {
        color: #718096;
    }
    
    .loading {
        color: #718096;
    }
    
    .loading-spinner {
        border: 2px solid rgba(99, 179, 237, 0.3);
        border-top-color: #63b3ed;
    }
    @endif
</style>
@endsection

@section('content')
<div class="category-container">
    <ul class="article-list" id="articleList">
        @forelse($articles as $article)
            <li class="article-item">
                <a href="{{ route('article.show', ['app_key' => $app_key, 'id' => $article->id]) }}" class="article-link">
                    {{ $article->title }}
                </a>
            </li>
        @empty
            <div class="empty-state">
                <i class="fa fa-file-text-o"></i>
                <p>暂无内容</p>
            </div>
        @endforelse
    </ul>
    
    <div class="loading" id="loading">
        <div class="loading-spinner"></div>
        <p>加载中...</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let page = 1;
        let loading = false;
        let hasMorePages = {{ $articles->hasMorePages() ? 'true' : 'false' }};
        let app_key = "{{ $app_key }}";
        let currentTheme = "{{ $theme }}";
        
        // 检查是否需要立即加载更多内容
        function checkInitialLoad() {
            // 如果内容高度小于视窗高度，且还有更多页面，则自动加载更多
            if ($(document).height() <= $(window).height() && hasMorePages) {
                loadMoreArticles();
            }
        }
        
        // 检测滚动到底部时加载更多
        $(window).scroll(function() {
            if (loading || !hasMorePages) return;
            
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
                loadMoreArticles();
            }
        });
        
        function loadMoreArticles() {
            loading = true;
            page++;
            $('#loading').show();
            
            $.ajax({
                url: "{{ route('article.category.load', ['app_key' => $app_key, 'id' => $category->id]) }}",
                data: { 
                    page: page,
                    theme: currentTheme
                },
                type: 'GET',
                success: function(response) {
                    if (response.articles.length > 0) {
                        let html = '';
                        response.articles.forEach(function(article) {
                            html += `
                                <li class="article-item">
                                    <a href="/article/${app_key}/${article.id}" class="article-link">
                                        ${article.title}
                                    </a>
                                </li>
                            `;
                        });
                        
                        $('#articleList').append(html);
                    }
                    
                    hasMorePages = response.has_more_pages;
                    loading = false;
                    $('#loading').hide();
                    
                    if (!hasMorePages) {
                        $('#loading').html('<p>没有更多内容了</p>').show();
                    } else {
                        // 再次检查是否需要加载更多
                        setTimeout(checkInitialLoad, 100);
                    }
                },
                error: function() {
                    loading = false;
                    $('#loading').html('<p>加载失败，请重试</p>').show();
                }
            });
        }
        
        // 页面加载完成后检查是否需要立即加载更多
        setTimeout(checkInitialLoad, 300);
    });
</script>
@endsection