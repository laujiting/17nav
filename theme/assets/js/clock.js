/**
 * 17Nav 世界时钟 - Leaflet + CartoDB 暗色地图
 */
(function() {
    'use strict';

    var cities = window.NAV_CLOCK_CITIES || [];
    var container = document.getElementById('clock-map');
    if (!container) return;

    // 动态加载 Leaflet CSS
    var cssLink = document.createElement('link');
    cssLink.rel = 'stylesheet';
    cssLink.href = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(cssLink);

    // 动态加载 Leaflet JS
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js';
    script.onload = initMap;
    document.head.appendChild(script);

    var map = null;
    var markers = [];

    function initMap() {
        map = L.map('clock-map', {
            zoomControl: true,
            attributionControl: false,
            minZoom: 1,
            maxZoom: 5,
            worldCopyJump: true
        }).setView([20, 100], 1);

        // CartoDB Dark Matter 瓦片
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        // 添加城市标记
        cities.forEach(function(c) {
            var marker = L.circleMarker([c.lat, c.lon], {
                radius: 4,
                fillColor: '#a5b4fc',
                color: '#a5b4fc',
                weight: 1,
                opacity: 0.8,
                fillOpacity: 0.9
            }).addTo(map);

            var bubbleContent = document.createElement('div');
            bubbleContent.className = 'clock-bubble-leaflet';
            bubbleContent.innerHTML =
                '<div class="clock-bubble-city">' + c.city + '</div>' +
                '<div class="clock-bubble-time" data-tz="' + c.timezone + '">--:--</div>' +
                '<div class="clock-bubble-date" data-tz="' + c.timezone + '">--</div>';

            marker.bindTooltip(bubbleContent, {
                permanent: true,
                direction: 'right',
                offset: [8, 0],
                className: 'clock-tooltip',
                interactive: false
            });

            markers.push({ marker: marker, city: c });
        });

        // 自动调整视野
        if (cities.length > 0) {
            var group = L.featureGroup(markers.map(function(m) { return m.marker; }));
            map.fitBounds(group.getBounds().pad(0.3), { maxZoom: 3 });
        }

        // 开始更新时间
        updateTime();
        setInterval(updateTime, 1000);
    }

    function updateTime() {
        var now = new Date();
        markers.forEach(function(m) {
            var timeEl = m.marker.getTooltip().getContent().querySelector('.clock-bubble-time');
            var dateEl = m.marker.getTooltip().getContent().querySelector('.clock-bubble-date');

            var time = now.toLocaleTimeString('zh-CN', {
                timeZone: m.city.timezone, hour: '2-digit', minute: '2-digit', hour12: false
            });
            var date = now.toLocaleDateString('zh-CN', {
                timeZone: m.city.timezone, month: '2-digit', day: '2-digit', weekday: 'short'
            });

            timeEl.textContent = time;
            dateEl.textContent = date;
        });
    }
})();
