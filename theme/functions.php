<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 17Nav 主题配置
 * Typecho 1.3.0+ 兼容
 */

function themeConfig($form)
{
    // 主题模式
    $themeMode = new \Typecho\Widget\Helper\Form\Element\Radio(
        'themeMode',
        array('auto' => '跟随系统', 'dark' => '暗色', 'light' => '亮色'),
        'auto', '主题模式', '选择默认主题模式'
    );
    $form->addInput($themeMode);

    // 背景图片
    $bgImage = new \Typecho\Widget\Helper\Form\Element\Text(
        'bgImage', null, '', '背景图片 URL', '留空则使用纯色背景'
    );
    $form->addInput($bgImage);

    // 背景模糊
    $bgBlur = new \Typecho\Widget\Helper\Form\Element\Text(
        'bgBlur', null, '0', '背景模糊度 (px)', '0-20，0 为不模糊'
    );
    $form->addInput($bgBlur);

    // 遮罩透明度
    $bgOpacity = new \Typecho\Widget\Helper\Form\Element\Text(
        'bgOpacity', null, '0', '遮罩透明度 (%)', '0-100，0 为无遮罩'
    );
    $form->addInput($bgOpacity);

    // 背景位置
    $bgPosition = new \Typecho\Widget\Helper\Form\Element\Select(
        'bgPosition',
        array(
            'center' => '居中', 'top' => '顶部', 'bottom' => '底部',
            'left' => '左侧', 'right' => '右侧',
            'top left' => '左上', 'top right' => '右上',
            'bottom left' => '左下', 'bottom right' => '右下'
        ),
        'center', '背景位置'
    );
    $form->addInput($bgPosition);

    // 背景缩放模式
    $bgSize = new \Typecho\Widget\Helper\Form\Element\Select(
        'bgSize',
        array('cover' => '填满裁切', 'contain' => '完整显示', '100% 100%' => '拉伸'),
        'cover', '背景缩放模式'
    );
    $form->addInput($bgSize);

    // 背景缩放比例
    $bgScale = new \Typecho\Widget\Helper\Form\Element\Text(
        'bgScale', null, '100', '背景缩放比例 (%)', '50-200，100 为原始大小'
    );
    $form->addInput($bgScale);

    // 世界时钟城市
    $clockCities = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'clockCities', null,
        "北京|Asia/Shanghai\n纽约|America/New_York\n伦敦|Europe/London",
        '世界时钟城市',
        '每行一个，格式：城市名|时区。可在下方下拉框快速添加'
    );
    $form->addInput($clockCities);

    // 天气城市
    $weatherCities = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'weatherCities', null,
        "北京|39.9|116.4\n上海|31.2|121.5",
        '天气城市',
        '每行一个，格式：城市名|纬度|经度。可在下方下拉框快速添加'
    );
    $form->addInput($weatherCities);

    // 注入 JS 增强（下拉框快速添加）
    ?>
    <script>
    (function() {
        // 预置时钟城市
        var clockPresets = [
            {city:'北京', tz:'Asia/Shanghai'},
            {city:'上海', tz:'Asia/Shanghai'},
            {city:'深圳', tz:'Asia/Shanghai'},
            {city:'香港', tz:'Asia/Hong_Kong'},
            {city:'台北', tz:'Asia/Taipei'},
            {city:'东京', tz:'Asia/Tokyo'},
            {city:'首尔', tz:'Asia/Seoul'},
            {city:'新加坡', tz:'Asia/Singapore'},
            {city:'悉尼', tz:'Australia/Sydney'},
            {city:'迪拜', tz:'Asia/Dubai'},
            {city:'孟买', tz:'Asia/Kolkata'},
            {city:'莫斯科', tz:'Europe/Moscow'},
            {city:'柏林', tz:'Europe/Berlin'},
            {city:'巴黎', tz:'Europe/Paris'},
            {city:'伦敦', tz:'Europe/London'},
            {city:'纽约', tz:'America/New_York'},
            {city:'芝加哥', tz:'America/Chicago'},
            {city:'洛杉矶', tz:'America/Los_Angeles'},
            {city:'多伦多', tz:'America/Toronto'},
            {city:'墨西哥城', tz:'America/Mexico_City'},
            {city:'圣保罗', tz:'America/Sao_Paulo'},
        ];

        // 预置天气城市
        var weatherPresets = [
            {city:'北京', lat:39.9, lon:116.4},
            {city:'上海', lat:31.2, lon:121.5},
            {city:'广州', lat:23.1, lon:113.3},
            {city:'深圳', lat:22.5, lon:114.1},
            {city:'成都', lat:30.6, lon:104.1},
            {city:'杭州', lat:30.3, lon:120.2},
            {city:'武汉', lat:30.6, lon:114.3},
            {city:'西安', lat:34.3, lon:108.9},
            {city:'南京', lat:32.1, lon:118.8},
            {city:'重庆', lat:29.6, lon:106.5},
            {city:'天津', lat:39.1, lon:117.2},
            {city:'苏州', lat:31.3, lon:120.6},
            {city:'长沙', lat:28.2, lon:112.9},
            {city:'青岛', lat:36.1, lon:120.4},
            {city:'大连', lat:38.9, lon:121.6},
            {city:'厦门', lat:24.5, lon:118.1},
            {city:'昆明', lat:25.0, lon:102.7},
            {city:'哈尔滨', lat:45.8, lon:126.5},
            {city:'乌鲁木齐', lat:43.8, lon:87.6},
            {city:'拉萨', lat:29.7, lon:91.1},
            {city:'香港', lat:22.3, lon:114.2},
            {city:'台北', lat:25.0, lon:121.5},
            {city:'东京', lat:35.7, lon:139.7},
            {city:'首尔', lat:37.6, lon:127.0},
            {city:'新加坡', lat:1.4, lon:103.8},
            {city:'纽约', lat:40.7, lon:-74.0},
            {city:'伦敦', lat:51.5, lon:-0.1},
            {city:'巴黎', lat:48.9, lon:2.4},
        ];

        function findTextarea(label) {
            var labels = document.querySelectorAll('.typecho-label, label, th');
            for (var i = 0; i < labels.length; i++) {
                if (labels[i].textContent.indexOf(label) >= 0) {
                    var row = labels[i].closest('.typecho-option, tr, .form-row');
                    if (row) {
                        var ta = row.querySelector('textarea');
                        if (ta) return ta;
                    }
                }
            }
            return null;
        }

        function addClockPicker() {
            var ta = findTextarea('世界时钟');
            if (!ta) return;
            var wrap = document.createElement('div');
            wrap.style.cssText = 'margin-top:0.5rem;display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap';
            wrap.innerHTML = '<select id="clock-preset" style="padding:0.3rem;border:1px solid #ddd;border-radius:0.3rem"><option value="">+ 添加城市...</option></select>';
            ta.parentNode.insertBefore(wrap, ta.nextSibling);

            var sel = wrap.querySelector('#clock-preset');
            clockPresets.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.city + '|' + c.tz;
                opt.textContent = c.city + ' (' + c.tz + ')';
                sel.appendChild(opt);
            });

            sel.addEventListener('change', function() {
                if (!sel.value) return;
                var line = sel.value;
                var existing = ta.value.trim();
                if (existing && existing.indexOf(line) < 0) {
                    ta.value = existing + '\n' + line;
                } else if (!existing) {
                    ta.value = line;
                }
                sel.value = '';
            });
        }

        function addWeatherPicker() {
            var ta = findTextarea('天气');
            if (!ta) return;
            var wrap = document.createElement('div');
            wrap.style.cssText = 'margin-top:0.5rem;display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap';
            wrap.innerHTML = '<select id="weather-preset" style="padding:0.3rem;border:1px solid #ddd;border-radius:0.3rem"><option value="">+ 添加城市...</option></select>';
            ta.parentNode.insertBefore(wrap, ta.nextSibling);

            var sel = wrap.querySelector('#weather-preset');
            weatherPresets.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.city + '|' + c.lat + '|' + c.lon;
                opt.textContent = c.city;
                sel.appendChild(opt);
            });

            sel.addEventListener('change', function() {
                if (!sel.value) return;
                var line = sel.value;
                var existing = ta.value.trim();
                if (existing && existing.indexOf(c.city) < 0) {
                    ta.value = existing + '\n' + line;
                } else if (!existing) {
                    ta.value = line;
                }
                sel.value = '';
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                addClockPicker();
                addWeatherPicker();
            });
        } else {
            addClockPicker();
            addWeatherPicker();
        }
    })();
    </script>
    <?php
}

/**
 * 获取主题设置（安全方式，不依赖插件）
 */
function nav17_get_theme_config($key, $default = '')
{
    $options = \Typecho\Widget::widget('Widget_Options');
    $val = $options->{$key};
    return $val !== null ? $val : $default;
}
