/**
 * 17Nav 主题切换 + 背景设置
 */
(function() {
    'use strict';

    var mode = window.NAV_CONFIG.themeMode || 'auto';

    function applyTheme() {
        var isDark;
        if (mode === 'auto') {
            isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        } else {
            isDark = mode === 'dark';
        }
        document.body.classList.toggle('dark', isDark);
    }

    function applyBackground() {
        var bg = window.NAV_CONFIG;
        var layer = document.getElementById('bg-layer');
        var overlay = document.getElementById('bg-overlay');
        if (!layer || !overlay) return;

        if (bg.bgImage) {
            layer.style.backgroundImage = 'url(' + bg.bgImage + ')';
            layer.style.filter = 'blur(' + (bg.bgBlur || 0) + 'px)';
            var scale = parseInt(bg.bgScale) || 100;
            layer.style.backgroundSize = bg.bgSize === '100% 100%'
                ? (scale + '% ' + scale + '%')
                : bg.bgSize;
            layer.style.backgroundPosition = bg.bgPosition || 'center';
            overlay.style.opacity = (parseInt(bg.bgOpacity) || 0) / 100;
        }
    }

    // 主题切换
    applyTheme();
    applyBackground();

    // 系统主题变化时自动切换
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
        if (mode === 'auto') applyTheme();
    });
})();
