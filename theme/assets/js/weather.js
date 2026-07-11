/**
 * 17Nav 天气组件
 * 数据源: Open-Meteo (免费, 无需 API key)
 */
(function() {
    'use strict';

    // 由后端注入: [{city, lat, lon}]
    var cities = window.NAV_WEATHER_CITIES || [
        {city: '北京', lat: 39.9, lon: 116.4},
        {city: '上海', lat: 31.2, lon: 121.5}
    ];

    var weatherIcons = {
        0: '☀️', 1: '🌤️', 2: '⛅', 3: '☁️',
        45: '🌫️', 48: '🌫️',
        51: '🌦️', 53: '🌦️', 55: '🌧️',
        61: '🌧️', 63: '🌧️', 65: '🌧️',
        71: '🌨️', 73: '🌨️', 75: '❄️',
        77: '❄️',
        80: '🌦️', 81: '🌧️', 82: '⛈️',
        95: '⛈️', 96: '⛈️', 99: '⛈️'
    };

    function fetchWeather(city, lat, lon) {
        var url = 'https://api.open-meteo.com/v1/forecast?latitude=' + lat +
            '&longitude=' + lon + '&current=temperature_2m,weather_code&timezone=auto';

        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var current = data.current;
                if (!current) return;
                var icon = weatherIcons[current.weather_code] || '🌡️';
                var temp = Math.round(current.temperature_2m);

                var list = document.getElementById('weather-list');
                if (!list) return;

                var existing = list.querySelector('[data-city="' + city + '"]');
                var html = '<span class="city">' + city + '</span>' +
                    '<span class="temp">' + icon + ' ' + temp + '°C</span>';

                if (existing) {
                    existing.innerHTML = html;
                } else {
                    var div = document.createElement('div');
                    div.className = 'weather-item';
                    div.setAttribute('data-city', city);
                    div.innerHTML = html;
                    list.appendChild(div);
                }
            })
            .catch(function(e) {
                console.warn('Weather fetch failed for ' + city + ':', e);
            });
    }

    function updateWeather() {
        cities.forEach(function(c) {
            fetchWeather(c.city, c.lat, c.lon);
        });
    }

    // 首次加载
    updateWeather();
    // 每 30 分钟刷新
    setInterval(updateWeather, 30 * 60 * 1000);
})();
