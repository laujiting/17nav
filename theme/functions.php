<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 17Nav 主题配置
 * Typecho 1.3.0+ 兼容
 */

function themeConfig($form)
{
    $themeMode = new \Typecho\Widget\Helper\Form\Element\Radio(
        'themeMode',
        array('auto' => '跟随系统', 'dark' => '暗色', 'light' => '亮色'),
        'auto', '主题模式', '选择默认主题模式'
    );
    $form->addInput($themeMode);

    $bgImage = new \Typecho\Widget\Helper\Form\Element\Text(
        'bgImage', null, '', '背景图片 URL', '留空则使用纯色背景'
    );
    $form->addInput($bgImage);

    $bgBlur = new \Typecho\Widget\Helper\Form\Element\Text(
        'bgBlur', null, '0', '背景模糊度 (px)', '0-20'
    );
    $form->addInput($bgBlur);

    $bgOpacity = new \Typecho\Widget\Helper\Form\Element\Text(
        'bgOpacity', null, '0', '遮罩透明度 (%)', '0-100'
    );
    $form->addInput($bgOpacity);

    $bgPosition = new \Typecho\Widget\Helper\Form\Element\Select(
        'bgPosition',
        array('center'=>'居中','top'=>'顶部','bottom'=>'底部','left'=>'左侧','right'=>'右侧',
            'top left'=>'左上','top right'=>'右上','bottom left'=>'左下','bottom right'=>'右下'),
        'center', '背景位置'
    );
    $form->addInput($bgPosition);

    $bgSize = new \Typecho\Widget\Helper\Form\Element\Select(
        'bgSize',
        array('cover'=>'填满裁切','contain'=>'完整显示','100% 100%'=>'拉伸'),
        'cover', '背景缩放模式'
    );
    $form->addInput($bgSize);

    $bgScale = new \Typecho\Widget\Helper\Form\Element\Text(
        'bgScale', null, '100', '背景缩放比例 (%)', '50-200'
    );
    $form->addInput($bgScale);

    // 世界时钟城市（带经纬度，用于地图标记）
    $clockCities = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'clockCities', null,
        "北京|39.9|116.4|Asia/Shanghai\n上海|31.2|121.5|Asia/Shanghai\n伦敦|51.5|-0.1|Europe/London\n纽约|40.7|-74.0|America/New_York",
        '世界时钟城市', '每行一个，格式：城市名|纬度|经度|时区。下方可快速添加'
    );
    $form->addInput($clockCities);

    // 天气城市
    $weatherCities = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'weatherCities', null,
        "北京|39.9|116.4\n上海|31.2|121.5",
        '天气城市', '每行一个，格式：城市名|纬度|经度。下方可快速添加'
    );
    $form->addInput($weatherCities);

    // 天气显示选项
    $weatherOptions = new \Typecho\Widget\Helper\Form\Element\Checkbox(
        'weatherOptions', array(
            'tempRange' => '温度范围（最低~最高）',
            'weatherType' => '天气类型（晴/阴/雨等）',
            'humidity' => '湿度',
            'sunrise' => '日出日落',
            'moonrise' => '月出月落',
        ),
        array('tempRange', 'weatherType', 'humidity', 'sunrise'),
        '天气显示项', '勾选需要显示的天气信息'
    );
    $form->addInput($weatherOptions);

    ?>
    <script>
    (function() {
        // ===== 预置时钟城市（全球主要城市，带经纬度） =====
        var clockPresets = [
            {city:'北京',lat:39.9,lon:116.4,tz:'Asia/Shanghai'},
            {city:'上海',lat:31.2,lon:121.5,tz:'Asia/Shanghai'},
            {city:'深圳',lat:22.5,lon:114.1,tz:'Asia/Shanghai'},
            {city:'香港',lat:22.3,lon:114.2,tz:'Asia/Hong_Kong'},
            {city:'台北',lat:25.0,lon:121.5,tz:'Asia/Taipei'},
            {city:'东京',lat:35.7,lon:139.7,tz:'Asia/Tokyo'},
            {city:'首尔',lat:37.6,lon:127.0,tz:'Asia/Seoul'},
            {city:'新加坡',lat:1.4,lon:103.8,tz:'Asia/Singapore'},
            {city:'悉尼',lat:-33.9,lon:151.2,tz:'Australia/Sydney'},
            {city:'迪拜',lat:25.3,lon:55.3,tz:'Asia/Dubai'},
            {city:'孟买',lat:19.1,lon:72.9,tz:'Asia/Kolkata'},
            {city:'莫斯科',lat:55.8,lon:37.6,tz:'Europe/Moscow'},
            {city:'柏林',lat:52.5,lon:13.4,tz:'Europe/Berlin'},
            {city:'巴黎',lat:48.9,lon:2.4,tz:'Europe/Paris'},
            {city:'伦敦',lat:51.5,lon:-0.1,tz:'Europe/London'},
            {city:'纽约',lat:40.7,lon:-74.0,tz:'America/New_York'},
            {city:'芝加哥',lat:41.9,lon:-87.6,tz:'America/Chicago'},
            {city:'洛杉矶',lat:34.1,lon:-118.2,tz:'America/Los_Angeles'},
            {city:'多伦多',lat:43.7,lon:-79.4,tz:'America/Toronto'},
            {city:'圣保罗',lat:-23.6,lon:-46.6,tz:'America/Sao_Paulo'},
        ];

        // ===== 中国省市数据（天气用） =====
        var chinaCities = {
            "北京": {"北京": {"东城":[39.9,116.4],"西城":[39.9,116.4],"朝阳":[39.9,116.4],"海淀":[39.9,116.4],"丰台":[39.9,116.4],"石景山":[39.9,116.4]}},
            "上海": {"上海": {"黄浦":[31.2,121.5],"徐汇":[31.2,121.4],"长宁":[31.2,121.4],"静安":[31.2,121.4],"浦东":[31.2,121.5]}},
            "广东": {"广州":[23.1,113.3],"深圳":[22.5,114.1],"珠海":[22.3,113.5],"佛山":[23.0,113.1],"东莞":[23.0,113.7],"中山":[22.5,113.4],"惠州":[23.1,114.4],"汕头":[23.4,116.7],"湛江":[21.2,110.4]},
            "浙江": {"杭州":[30.3,120.2],"宁波":[29.9,121.6],"温州":[28.0,120.7],"绍兴":[30.0,120.6],"嘉兴":[30.8,120.8],"金华":[29.1,119.6]},
            "江苏": {"南京":[32.1,118.8],"苏州":[31.3,120.6],"无锡":[31.5,120.3],"常州":[31.8,119.9],"南通":[32.0,120.9],"徐州":[34.3,117.2]},
            "四川": {"成都":[30.6,104.1],"绵阳":[31.5,104.7],"自贡":[29.4,104.8],"南充":[30.8,106.1],"宜宾":[28.8,104.6]},
            "湖北": {"武汉":[30.6,114.3],"宜昌":[30.7,111.3],"襄阳":[32.0,112.1],"荆州":[30.3,112.2]},
            "湖南": {"长沙":[28.2,112.9],"株洲":[27.8,113.1],"湘潭":[27.8,112.9],"衡阳":[26.9,112.6]},
            "福建": {"福州":[26.1,119.3],"厦门":[24.5,118.1],"泉州":[24.9,118.6],"漳州":[24.5,117.7]},
            "山东": {"济南":[36.7,117.0],"青岛":[36.1,120.4],"烟台":[37.5,121.4],"潍坊":[36.7,119.1],"临沂":[35.1,118.4]},
            "河南": {"郑州":[34.7,113.6],"洛阳":[34.6,112.4],"开封":[34.8,114.3],"新乡":[35.3,113.9]},
            "河北": {"石家庄":[38.0,114.5],"唐山":[39.6,118.2],"保定":[38.9,115.5],"廊坊":[39.5,116.7]},
            "陕西": {"西安":[34.3,108.9],"宝鸡":[34.4,107.1],"咸阳":[34.3,108.7],"渭南":[34.5,109.5]},
            "辽宁": {"沈阳":[41.8,123.4],"大连":[38.9,121.6],"鞍山":[41.1,123.0],"抚顺":[41.9,123.9]},
            "黑龙江": {"哈尔滨":[45.8,126.5],"齐齐哈尔":[47.4,123.9],"大庆":[46.6,125.1]},
            "吉林": {"长春":[43.9,125.3],"吉林":[43.8,126.5],"延边":[42.9,129.5]},
            "安徽": {"合肥":[31.8,117.3],"芜湖":[31.3,118.4],"蚌埠":[32.9,117.4]},
            "江西": {"南昌":[28.7,115.9],"赣州":[25.9,114.9],"九江":[29.7,116.0]},
            "重庆": {"重庆":[29.6,106.5],"万州":[30.8,108.4],"涪陵":[29.7,107.4]},
            "天津": {"天津":[39.1,117.2]},
            "云南": {"昆明":[25.0,102.7],"大理":[25.7,100.2],"丽江":[26.9,100.2]},
            "贵州": {"贵阳":[26.6,106.7],"遵义":[27.7,106.9]},
            "甘肃": {"兰州":[36.1,103.8],"天水":[34.6,105.7]},
            "青海": {"西宁":[36.6,101.8]},
            "海南": {"海口":[20.0,110.3],"三亚":[18.3,109.5]},
            "新疆": {"乌鲁木齐":[43.8,87.6],"喀什":[39.5,75.9]},
            "西藏": {"拉萨":[29.7,91.1],"日喀则":[29.3,88.9]},
            "内蒙古": {"呼和浩特":[40.8,111.7],"包头":[40.7,109.8]},
            "宁夏": {"银川":[38.5,106.2]},
            "广西": {"南宁":[22.8,108.4],"桂林":[25.3,110.3],"柳州":[24.3,109.4]},
            "山西": {"太原":[37.9,112.5],"大同":[40.1,113.3]},
        };

        function findTextarea(label) {
            var labels = document.querySelectorAll('.typecho-label, label, th, .typecho-option-title');
            for (var i = 0; i < labels.length; i++) {
                if (labels[i].textContent.indexOf(label) >= 0) {
                    var row = labels[i].closest('.typecho-option, tr, .form-row, .typecho-option-row');
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
            var sel = document.createElement('select');
            sel.style.cssText = 'padding:0.3rem;border:1px solid #ddd;border-radius:0.3rem';
            sel.innerHTML = '<option value="">+ 添加时钟城市...</option>';
            clockPresets.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.city + '|' + c.lat + '|' + c.lon + '|' + c.tz;
                opt.textContent = c.city + ' (' + c.tz + ')';
                sel.appendChild(opt);
            });
            wrap.appendChild(sel);
            ta.parentNode.insertBefore(wrap, ta.nextSibling);

            sel.addEventListener('change', function() {
                if (!sel.value) return;
                var cityName = sel.value.split('|')[0];
                var lines = ta.value.trim().split('\n').map(function(l) { return l.split('|')[0].trim(); });
                if (lines.indexOf(cityName) < 0) {
                    ta.value = ta.value.trim() ? ta.value.trim() + '\n' + sel.value : sel.value;
                }
                sel.value = '';
            });
        }

        function addWeatherPicker() {
            var ta = findTextarea('天气');
            if (!ta) return;
            var wrap = document.createElement('div');
            wrap.style.cssText = 'margin-top:0.5rem;display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap';

            var provSel = document.createElement('select');
            provSel.style.cssText = 'padding:0.3rem;border:1px solid #ddd;border-radius:0.3rem';
            provSel.innerHTML = '<option value="">省份</option>';

            var citySel = document.createElement('select');
            citySel.style.cssText = 'padding:0.3rem;border:1px solid #ddd;border-radius:0.3rem';
            citySel.innerHTML = '<option value="">城市</option>';

            var distSel = document.createElement('select');
            distSel.style.cssText = 'padding:0.3rem;border:1px solid #ddd;border-radius:0.3rem;display:none';
            distSel.innerHTML = '<option value="">区县</option>';

            var addBtn = document.createElement('button');
            addBtn.type = 'button';
            addBtn.textContent = '添加';
            addBtn.style.cssText = 'padding:0.3rem 0.8rem;border:1px solid #312e81;background:#312e81;color:#fff;border-radius:0.3rem;cursor:pointer';

            wrap.appendChild(provSel);
            wrap.appendChild(citySel);
            wrap.appendChild(distSel);
            wrap.appendChild(addBtn);
            ta.parentNode.insertBefore(wrap, ta.nextSibling);

            // 填充省份
            Object.keys(chinaCities).forEach(function(p) {
                var opt = document.createElement('option');
                opt.value = p;
                opt.textContent = p;
                provSel.appendChild(opt);
            });

            // 省份变化 -> 填充城市
            provSel.addEventListener('change', function() {
                citySel.innerHTML = '<option value="">城市</option>';
                distSel.style.display = 'none';
                distSel.innerHTML = '<option value="">区县</option>';
                var prov = chinaCities[provSel.value];
                if (!prov) return;
                Object.keys(prov).forEach(function(c) {
                    var opt = document.createElement('option');
                    opt.value = c;
                    opt.textContent = c;
                    citySel.appendChild(opt);
                });
            });

            // 城市变化 -> 填充区县（如果有的话）
            citySel.addEventListener('change', function() {
                distSel.innerHTML = '<option value="">区县</option>';
                var prov = chinaCities[provSel.value];
                if (!prov) return;
                var city = prov[citySel.value];
                if (!city) return;
                if (Array.isArray(city)) {
                    // 直辖市/简单格式 [lat, lon]
                    distSel.style.display = 'none';
                } else {
                    // 有区县
                    distSel.style.display = '';
                    Object.keys(city).forEach(function(d) {
                        var opt = document.createElement('option');
                        opt.value = d;
                        opt.textContent = d;
                        distSel.appendChild(opt);
                    });
                }
            });

            // 添加按钮
            addBtn.addEventListener('click', function() {
                var prov = provSel.value;
                var city = citySel.value;
                if (!prov || !city) return;

                var lat, lon, name;
                var cityData = chinaCities[prov][city];
                if (Array.isArray(cityData)) {
                    lat = cityData[0]; lon = cityData[1];
                    name = city;
                } else if (distSel.value && distSel.style.display !== 'none') {
                    var dist = cityData[distSel.value];
                    lat = dist[0]; lon = dist[1];
                    name = distSel.value;
                } else {
                    // 取第一个区县
                    var firstKey = Object.keys(cityData)[0];
                    lat = cityData[firstKey][0]; lon = cityData[firstKey][1];
                    name = firstKey;
                }

                var line = name + '|' + lat + '|' + lon;
                var lines = ta.value.trim().split('\n').map(function(l) { return l.split('|')[0].trim(); });
                if (lines.indexOf(name) < 0) {
                    ta.value = ta.value.trim() ? ta.value.trim() + '\n' + line : line;
                }
                provSel.value = ''; citySel.value = '';
                distSel.style.display = 'none'; distSel.value = '';
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

function nav17_get_theme_config($key, $default = '')
{
    $options = \Typecho\Widget::widget('Widget_Options');
    $val = $options->{$key};
    return $val !== null ? $val : $default;
}
