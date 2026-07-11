/**
 * 17Nav 世界时钟 - 地图版
 * 城市按经纬度标记在世界地图上，气泡框显示时间
 * 自适应布局：避免遮挡和边缘裁切
 */
(function() {
    'use strict';

    var cities = window.NAV_CLOCK_CITIES || [];
    var container = document.getElementById('clock-map');
    if (!container) return;

    function project(lat, lon) {
        var x = (lon + 180) / 360 * 100;
        var y = (90 - lat) / 180 * 100;
        return {x: x, y: y};
    }

    function render() {
        var now = new Date();
        container.innerHTML = '';

        // 先计算所有标记点位置
        var markers = cities.map(function(c) {
            var pos = project(c.lat, c.lon);
            var time = now.toLocaleTimeString('zh-CN', {
                timeZone: c.timezone, hour: '2-digit', minute: '2-digit', hour12: false
            });
            var date = now.toLocaleDateString('zh-CN', {
                timeZone: c.timezone, month: '2-digit', day: '2-digit', weekday: 'short'
            });
            return { city: c, pos: pos, time: time, date: date };
        });

        // 按经度从西到东排序，检测重叠
        markers.sort(function(a, b) { return a.pos.x - b.pos.x; });

        // 气泡框宽度估算（约 70px），地图宽度动态获取
        var mapWidth = container.clientWidth || 300;
        var bubbleWidth = 70;
        var minGap = 4; // 气泡之间最小间距 px

        // 计算每个气泡的理想 X 中心位置（像素）
        markers.forEach(function(m) {
            m.pixelX = m.pos.x / 100 * mapWidth;
        });

        // 碰撞检测：从左到右，如果重叠就交替上/下错开
        for (var i = 1; i < markers.length; i++) {
            var prev = markers[i - 1];
            var curr = markers[i];
            var prevRight = prev.pixelX + bubbleWidth / 2;
            var currLeft = curr.pixelX - bubbleWidth / 2;

            // 如果 Y 距离也近（< 20%），才需要错开
            if (Math.abs(prev.pos.y - curr.pos.y) < 15) {
                if (currLeft < prevRight + minGap) {
                    // Y 方向交替错开
                    curr.altY = prev.altY === 'up' ? 'down' : 'up';
                }
            }
        }

        // 渲染
        markers.forEach(function(m) {
            var dot = document.createElement('div');
            dot.className = 'clock-marker';
            dot.style.left = m.pos.x + '%';
            dot.style.top = m.pos.y + '%';

            var bubble = document.createElement('div');
            bubble.className = 'clock-bubble';

            bubble.innerHTML =
                '<div class="clock-bubble-city">' + m.city.city + '</div>' +
                '<div class="clock-bubble-time">' + m.time + '</div>' +
                '<div class="clock-bubble-date">' + m.date + '</div>';

            // X 方向：自动避让左右边缘
            var xPct = m.pos.x;
            if (xPct > 75) {
                // 靠右边缘 -> 气泡在标记点左侧
                bubble.style.right = '100%';
                bubble.style.left = 'auto';
                bubble.style.marginRight = '6px';
                bubble.style.marginLeft = '0';
                bubble.style.transform = 'translateY(-50%)';
            } else if (xPct < 25) {
                // 靠左边缘 -> 气泡在标记点右侧
                bubble.style.left = '100%';
                bubble.style.right = 'auto';
                bubble.style.marginLeft = '6px';
                bubble.style.marginRight = '0';
                bubble.style.transform = 'translateY(-50%)';
            } else {
                // 中间区域 -> 默认右侧
                bubble.style.left = '100%';
                bubble.style.right = 'auto';
                bubble.style.marginLeft = '6px';
                bubble.style.marginRight = '0';
                bubble.style.transform = 'translateY(-50%)';
            }

            // Y 方向：自动避让上下边缘 + 重叠错开
            var yPct = m.pos.y;
            if (m.altY === 'up' || yPct < 20) {
                bubble.style.bottom = '100%';
                bubble.style.top = 'auto';
                bubble.style.transform = 'translateY(0)';
                if (m.altY === 'up') {
                    bubble.style.marginBottom = '14px';
                } else {
                    bubble.style.marginBottom = '6px';
                }
            } else if (yPct > 80) {
                bubble.style.top = '100%';
                bubble.style.bottom = 'auto';
                bubble.style.transform = 'translateY(0)';
                bubble.style.marginTop = '6px';
            } else {
                // 默认：垂直居中
                bubble.style.top = '50%';
                bubble.style.bottom = 'auto';
                bubble.style.transform = 'translateY(-50%)';
            }

            dot.appendChild(bubble);
            container.appendChild(dot);
        });
    }

    render();
    // 每秒更新时间
    setInterval(render, 1000);
    // 窗口大小变化时重新布局
    var resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(render, 200);
    });
})();

