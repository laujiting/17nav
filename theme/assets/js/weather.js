/**
 * 17Nav 天气组件 - 增强版
 * 数据源: Open-Meteo (免费, 无需 API key)
 * 支持: 温度范围/天气类型/湿度/日出日落/月出月落
 */
(function() {
    'use strict';

    var cities = window.NAV_WEATHER_CITIES || [];
    var options = window.NAV_WEATHER_OPTIONS || ['tempRange', 'weatherType', 'humidity', 'sunrise'];

    function hasOption(key) {
        return options.indexOf(key) >= 0;
    }

    var weatherIcons = {
        0: '☀️', 1: '🌤️', 2: '⛅', 3: '☁️',
        45: '🌫️', 48: '🌫️',
        51: '🌦️', 53: '🌦️', 55: '🌧️',
        56: '🌧️', 57: '🌧️',
        61: '🌧️', 63: '🌧️', 65: '🌧️',
        66: '🌧️', 67: '🌧️',
        71: '🌨️', 73: '🌨️', 75: '❄️',
        77: '❄️',
        80: '🌦️', 81: '🌧️', 82: '⛈️',
        85: '🌨️', 86: '❄️',
        95: '⛈️', 96: '⛈️', 99: '⛈️'
    };

    var weatherNames = {
        0: '晴', 1: '晴间多云', 2: '多云', 3: '阴',
        45: '雾', 48: '冻雾',
        51: '毛毛雨', 53: '小雨', 55: '中雨',
        56: '冻雨', 57: '冻雨',
        61: '小雨', 63: '中雨', 65: '大雨',
        66: '冻雨', 67: '冻雨',
        71: '小雪', 73: '中雪', 75: '大雪',
        77: '雪粒',
        80: '阵雨', 81: '中阵雨', 82: '大阵雨',
        85: '阵雪', 86: '大雪',
        95: '雷阵雨', 96: '雷阵雨冰雹', 99: '雷阵雨冰雹'
    };

    // 简易月相计算
    function getMoonPhase(date) {
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var r = year % 100;
        r %= 19;
        if (r > 9) r -= 19;
        r = ((r * 11) % 30) + month + day;
        if (month < 3) r += 2;
        r -= ((year < 2000) ? 4 : 8.3);
        r = Math.floor(r + 0.5) % 30;
        return (r < 0 ? r + 30 : r) / 29.53;
    }

    function moonPhaseName(phase) {
        if (phase < 0.03 || phase > 0.97) return '🌑 新月';
        if (phase < 0.22) return ' waxing ' + '🌒 蛾眉月';
        if (phase < 0.28) return '🌓 上弦月';
        if (phase < 0.47) return '🌔 盈凸月';
        if (phase < 0.53) return '🌕 满月';
        if (phase < 0.72) return '🌖 亏凸月';
        if (phase < 0.78) return '🌗 下弦月';
        return '🌘 残月';
    }

    function formatTime(isoStr) {
        if (!isoStr) return '--:--';
        var d = new Date(isoStr);
        return d.toLocaleTimeString('zh-CN', {hour: '2-digit', minute: '2-digit', hour12: false});
    }

    function fetchWeather(city, lat, lon) {
        var params = 'current=temperature_2m,relative_humidity_2m,weather_code';
        if (hasOption('tempRange') || hasOption('weatherType')) {
            params += '&daily=temperature_2m_max,temperature_2m_min,weather_code';
        }
        if (hasOption('sunrise')) {
            params += ',sunrise,sunset';
        }
        params += '&timezone=auto&forecast_days=1';

        var url = 'https://api.open-meteo.com/v1/forecast?latitude=' + lat +
            '&longitude=' + lon + '&' + params;

        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var html = buildWeatherHtml(city, data);
                var list = document.getElementById('weather-list');
                if (!list) return;

                var existing = list.querySelector('[data-city="' + city + '"]');
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

    function buildWeatherHtml(city, data) {
        var current = data.current || {};
        var daily = data.daily || {};
        var daily0 = {};
        if (daily.time && daily.time.length > 0) {
            for (var k in daily) {
                if (k !== 'time') daily0[k] = daily[k][0];
            }
        }

        var icon = weatherIcons[current.weather_code] || weatherIcons[daily0.weather_code] || '🌡️';
        var weatherName = weatherNames[current.weather_code] || weatherNames[daily0.weather_code] || '未知';
        var currentTemp = current.temperature_2m != null ? Math.round(current.temperature_2m) : '--';
        var minTemp = daily0.temperature_2m_min != null ? Math.round(daily0.temperature_2m_min) : '--';
        var maxTemp = daily0.temperature_2m_max != null ? Math.round(daily0.temperature_2m_max) : '--';
        var humidity = current.relative_humidity_2m != null ? current.relative_humidity_2m : '--';
        var sunrise = formatTime(daily0.sunrise);
        var sunset = formatTime(daily0.sunset);

        var html = '<div class="weather-city">' + city + '</div>';
        html += '<div class="weather-main">';

        // 天气类型
        if (hasOption('weatherType')) {
            html += '<span class="weather-icon">' + icon + '</span>';
            html += '<span class="weather-type">' + weatherName + '</span>';
        }

        // 温度范围
        if (hasOption('tempRange')) {
            html += '<span class="weather-temp-range">' + minTemp + '° ~ ' + maxTemp + '°C</span>';
        } else {
            html += '<span class="weather-temp">' + currentTemp + '°C</span>';
        }

        html += '</div>';

        // 详细信息
        var details = [];
        if (hasOption('humidity')) {
            details.push('<span class="weather-detail">💧 ' + humidity + '%</span>');
        }
        if (hasOption('sunrise')) {
            details.push('<span class="weather-detail">🌅 ' + sunrise + '</span>');
            details.push('<span class="weather-detail">🌇 ' + sunset + '</span>');
        }
        if (hasOption('moonrise')) {
            var phase = getMoonPhase(new Date());
            details.push('<span class="weather-detail">' + moonPhaseName(phase) + '</span>');
        }

        if (details.length > 0) {
            html += '<div class="weather-details">' + details.join('') + '</div>';
        }

        return html;
    }

    function updateWeather() {
        cities.forEach(function(c) {
            fetchWeather(c.city, c.lat, c.lon);
        });
    }

    updateWeather();
    setInterval(updateWeather, 30 * 60 * 1000);
})();
