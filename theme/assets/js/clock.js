/**
 * 17Nav 世界时钟 - 地图版
 * 碰撞检测 + 自适应避让算法
 */
(function() {
    'use strict';

    var cities = window.NAV_CLOCK_CITIES || [];
    var container = document.getElementById('clock-map');
    if (!container) return;

    function project(lat, lon) {
        return {
            x: (lon + 180) / 360 * 100,
            y: (90 - lat) / 180 * 100
        };
    }

    // 气泡框尺寸估算
    var BUBBLE_W = 72;
    var BUBBLE_H = 48;
    var GAP = 6;
    var EDGE_PAD = 10; // 距离地图边缘最小距离

    function getTimeData(c, now) {
        var time = now.toLocaleTimeString('zh-CN', {
            timeZone: c.timezone, hour: '2-digit', minute: '2-digit', hour12: false
        });
        var date = now.toLocaleDateString('zh-CN', {
            timeZone: c.timezone, month: '2-digit', day: '2-digit', weekday: 'short'
        });
        return { time: time, date: date };
    }

    function rectsOverlap(a, b) {
        return !(a.x + a.w < b.x || b.x + b.w < a.x || a.y + a.h < b.y || b.y + b.h < a.y);
    }

    function render() {
        var now = new Date();
        container.innerHTML = '';

        if (cities.length === 0) return;

        var mapW = container.clientWidth || 300;
        var mapH = container.clientHeight || (mapW / 2);

        // 计算每个标记的像素坐标
        var markers = cities.map(function(c) {
            var pos = project(c.lat, c.lon);
            var td = getTimeData(c, now);
            return {
                city: c.city,
                pxX: pos.x / 100 * mapW,  // 标记点像素 X
                pxY: pos.y / 100 * mapH,  // 标记点像素 Y
                time: td.time,
                date: td.date,
                // 气泡框位置（初始为标记点右上方），会后面调整
                bubbleX: 0,
                bubbleY: 0,
                placed: false
            };
        });

        // 每个气泡有 4 个候选位置：右上、右下、左上、左下
        // 偏移量相对于标记点
        var offsets = [
            { dx: GAP, dy: -BUBBLE_H - GAP },        // 右上
            { dx: GAP, dy: GAP },                     // 右下
            { dx: -BUBBLE_W - GAP, dy: -BUBBLE_H - GAP }, // 左上
            { dx: -BUBBLE_W - GAP, dy: GAP },         // 左下
        ];

        var placed = [];

        // 按经度排序（从西到东），减少交叉
        markers.sort(function(a, b) { return a.pxX - b.pxX; });

        markers.forEach(function(m) {
            var bestOffset = null;
            var bestScore = -Infinity;

            for (var i = 0; i < offsets.length; i++) {
                var off = offsets[i];
                var bx = m.pxX + off.dx;
                var by = m.pxY + off.dy;

                // 边界检查（留出 EDGE_PAD 的边距）
                if (bx < EDGE_PAD || bx + BUBBLE_W > mapW - EDGE_PAD || by < EDGE_PAD || by + BUBBLE_H > mapH - EDGE_PAD) {
                    bx = Math.max(EDGE_PAD, Math.min(bx, mapW - BUBBLE_W - EDGE_PAD));
                    by = Math.max(EDGE_PAD, Math.min(by, mapH - BUBBLE_H - EDGE_PAD));
                }

                var rect = { x: bx, y: by, w: BUBBLE_W, h: BUBBLE_H };

                // 碰撞检测
                var hasCollision = false;
                for (var j = 0; j < placed.length; j++) {
                    if (rectsOverlap(rect, placed[j])) {
                        hasCollision = true;
                        break;
                    }
                }

                // 评分：无碰撞 > 有碰撞；离标记点近 > 远
                var distScore = -Math.abs(bx + BUBBLE_W/2 - m.pxX) - Math.abs(by + BUBBLE_H/2 - m.pxY);
                var score = (hasCollision ? -10000 : 0) + distScore;

                // 优先选无碰撞的
                if (!hasCollision) {
                    bestOffset = { bx: bx, by: by };
                    break;
                }

                if (score > bestScore) {
                    bestScore = score;
                    bestOffset = { bx: bx, by: by };
                }
            }

            m.bubbleX = bestOffset.bx;
            m.bubbleY = bestOffset.by;
            m.placed = true;

            placed.push({ x: m.bubbleX, y: m.bubbleY, w: BUBBLE_W, h: BUBBLE_H });
        });

        // 如果还有碰撞（4个位置都不行），尝试 Y 方向大幅错开
        for (var i = 0; i < placed.length; i++) {
            for (var j = i + 1; j < placed.length; j++) {
                if (rectsOverlap(placed[i], placed[j])) {
                    // 把后面的往上推
                    var pushUp = placed[j].y - BUBBLE_H - GAP;
                    if (pushUp < EDGE_PAD) pushUp = placed[j].y + BUBBLE_H + GAP;
                    if (pushUp + BUBBLE_H > mapH - EDGE_PAD) pushUp = mapH - BUBBLE_H - EDGE_PAD;
                    placed[j].y = pushUp;
                    markers[j].bubbleY = pushUp;
                }
            }
        }

        // 渲染
        markers.forEach(function(m) {
            var dot = document.createElement('div');
            dot.className = 'clock-marker';
            dot.style.left = (m.pxX / mapW * 100) + '%';
            dot.style.top = (m.pxY / mapH * 100) + '%';

            // 连接线（标记点到气泡框）
            var connector = document.createElement('div');
            connector.className = 'clock-connector';
            var cx = m.pxX;
            var cy = m.pxY;
            var bx = m.bubbleX + BUBBLE_W / 2;
            var by = m.bubbleY + BUBBLE_H / 2;
            var dx = bx - cx;
            var dy = by - cy;
            var len = Math.sqrt(dx * dx + dy * dy);
            var angle = Math.atan2(dy, dx) * 180 / Math.PI;
            connector.style.cssText = 'position:absolute;left:' + (cx / mapW * 100) + '%;top:' + (cy / mapH * 100) + '%;' +
                'width:' + len + 'px;height:1px;background:rgba(165,180,252,0.3);' +
                'transform-origin:0 0;transform:rotate(' + angle + 'deg);' +
                'pointer-events:none;z-index:1';

            container.appendChild(connector);

            // 气泡框（绝对定位，像素坐标）
            var bubble = document.createElement('div');
            bubble.className = 'clock-bubble';
            bubble.style.cssText =
                'position:absolute;left:' + m.bubbleX + 'px;top:' + m.bubbleY + 'px;' +
                'transform:none;margin:0;';
            bubble.innerHTML =
                '<div class="clock-bubble-city">' + m.city + '</div>' +
                '<div class="clock-bubble-time">' + m.time + '</div>' +
                '<div class="clock-bubble-date">' + m.date + '</div>';

            container.appendChild(bubble);

            // 标记点本身
            dot.style.transform = 'translate(-50%, -50%)';
            container.appendChild(dot);
        });
    }

    render();
    setInterval(render, 1000);
    var resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(render, 200);
    });
})();
