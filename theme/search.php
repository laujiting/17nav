<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<div class="search-bar">
    <div class="search-engine">
        <select id="search-engine">
            <option value="https://www.baidu.com/s?wd=">百度</option>
            <option value="https://www.google.com/search?q=">Google</option>
            <option value="https://www.bing.com/search?q=">Bing</option>
            <option value="https://github.com/search?q=">GitHub</option>
        </select>
    </div>
    <input type="text" id="search-input" placeholder="搜索书签或输入关键词..." autocomplete="off">
    <button id="search-btn">🔍</button>
    <div class="search-results" id="search-results"></div>
</div>
