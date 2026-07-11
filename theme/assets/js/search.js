/**
 * 17Nav 搜索 + 悬浮结果
 */
(function() {
    'use strict';

    var input = document.getElementById('search-input');
    var results = document.getElementById('search-results');
    var btn = document.getElementById('search-btn');
    var engine = document.getElementById('search-engine');

    // 书签数据（由后端注入）
    var bookmarks = window.NAV_BOOKMARKS || [];

    function search(keyword) {
        if (!keyword || keyword.trim().length === 0) {
            results.classList.remove('show');
            return;
        }
        var kw = keyword.trim().toLowerCase();
        var matched = bookmarks.filter(function(b) {
            return (b.name && b.name.toLowerCase().indexOf(kw) >= 0) ||
                   (b.desc && b.desc.toLowerCase().indexOf(kw) >= 0) ||
                   (b.tags && b.tags.some(function(t) { return t.toLowerCase().indexOf(kw) >= 0; }));
        });

        if (matched.length === 0) {
            results.innerHTML = '<div class="search-result-item" style="justify-content:center;color:var(--text-muted)">无匹配结果 🔍</div>';
        } else {
            results.innerHTML = matched.slice(0, 10).map(function(b) {
                var icon = b.icon
                    ? '<img src="' + b.icon + '" onerror="this.style.display=\'none\'">'
                    : '<div class="icon-placeholder" style="background:' + b.color + '">' + b.name[0] + '</div>';
                return '<div class="search-result-item" data-url="' + b.url + '">' +
                    icon +
                    '<div><div class="name">' + b.name + '</div>' +
                    '<div class="desc">' + (b.desc || '') + '</div></div>' +
                    '</div>';
            }).join('');
        }
        results.classList.add('show');
    }

    function openExternal() {
        var kw = input.value.trim();
        if (!kw) return;
        var engineUrl = engine.value + encodeURIComponent(kw);
        window.open(engineUrl, '_blank');
    }

    // 输入搜索
    var debounceTimer;
    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            search(input.value);
        }, 150);
    });

    // 回车搜索
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            // 如果有站内结果，直接打开第一个
            var first = results.querySelector('.search-result-item[data-url]');
            if (first && results.classList.contains('show')) {
                window.open(first.dataset.url, '_blank');
            } else {
                openExternal();
            }
        }
    });

    // 搜索按钮
    btn.addEventListener('click', openExternal);

    // 点击搜索结果
    results.addEventListener('click', function(e) {
        var item = e.target.closest('.search-result-item');
        if (item && item.dataset.url) {
            window.open(item.dataset.url, '_blank');
            // 统计点击
            if (window.NAV_CLICK_TRACK) {
                window.NAV_CLICK_TRACK(item.dataset.url);
            }
        }
    });

    // 点击外部关闭
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-bar')) {
            results.classList.remove('show');
        }
    });
})();
