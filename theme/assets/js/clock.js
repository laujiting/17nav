/**
 * 17Nav 世界时钟
 */
(function() {
    'use strict';

    // 由后端注入: [{city, timezone}]
    var cities = window.NAV_CLOCK_CITIES || [
        {city: '北京', timezone: 'Asia/Shanghai'},
        {city: '纽约', timezone: 'America/New_York'},
        {city: '伦敦', timezone: 'Europe/London'}
    ];

    function updateClock() {
        var list = document.getElementById('clock-list');
        if (!list) return;

        var now = new Date();
        list.innerHTML = cities.map(function(c) {
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
            return '<div class="clock-item">' +
                '<span class="city">' + c.city + '</span>' +
                '<span class="time">' + time + '</span>' +
                '</div>';
        }).join('');
    }

    updateClock();
    setInterval(updateClock, 1000);
})();
