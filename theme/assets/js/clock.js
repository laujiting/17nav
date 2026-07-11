/**
 * 17Nav 世界时钟 - 地图版
 * 城市按经纬度标记在世界地图上，气泡框显示时间
 */
(function() {
    'use strict';

    var cities = window.NAV_CLOCK_CITIES || [];

    var container = document.getElementById('clock-map');
    if (!container) return;

    // 等距圆柱投影：lon(-180~180) -> x(0~100%), lat(90~-90) -> y(0~100%)
    function project(lat, lon) {
        var x = (lon + 180) / 360 * 100;
        var y = (90 - lat) / 180 * 100;
        return {x: x, y: y};
    }

    function updateClock() {
        var now = new Date();

        // 清空容器
        container.innerHTML = '';

        cities.forEach(function(c) {
            var pos = project(c.lat, c.lon);

            // 标记点
            var dot = document.createElement('div');
            dot.className = 'clock-marker';
            dot.style.left = pos.x + '%';
            dot.style.top = pos.y + '%';

            // 气泡框
            var bubble = document.createElement('div');
            bubble.className = 'clock-bubble';

            var time = now.toLocaleTimeString('zh-CN', {
                timeZone: c.timezone,
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            var date = now.toLocaleDateString('zh-CN', {
                timeZone: c.timezone,
                month: '2-digit',
                day: '2-digit',
                weekday: 'short'
            });

            bubble.innerHTML =
                '<div class="clock-bubble-city">' + c.city + '</div>' +
                '<div class="clock-bubble-time">' + time + '</div>' +
                '<div class="clock-bubble-date">' + date + '</div>';

            // 根据经度决定气泡在标记点左侧还是右侧（避免被边缘裁切）
            if (pos.x > 70) {
                bubble.style.right = '100%';
                bubble.style.marginRight = '6px';
            } else {
                bubble.style.left = '100%';
                bubble.style.marginLeft = '6px';
            }

            // 根据纬度决定气泡在标记点上侧还是下侧
            if (pos.y > 70) {
                bubble.style.bottom = '100%';
                bubble.style.marginBottom = '6px';
                bubble.style.top = 'auto';
            }

            dot.appendChild(bubble);
            container.appendChild(dot);
        });
    }

    updateClock();
    setInterval(updateClock, 1000);
})();
