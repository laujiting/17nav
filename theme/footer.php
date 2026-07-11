<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 注入时钟城市数据（格式：城市名|纬度|经度|时区）
$clockCitiesRaw = nav17_get_theme_config('clockCities', "北京|39.9|116.4|Asia/Shanghai\n上海|31.2|121.5|Asia/Shanghai\n伦敦|51.5|-0.1|Europe/London\n纽约|40.7|-74.0|America/New_York");
$clockCities = array();
foreach (explode("\n", $clockCitiesRaw) as $line) {
    $line = trim($line);
    if (empty($line)) continue;
    $parts = explode('|', $line);
    if (count($parts) >= 4) {
        $clockCities[] = array(
            'city' => trim($parts[0]),
            'lat' => floatval(trim($parts[1])),
            'lon' => floatval(trim($parts[2])),
            'timezone' => trim($parts[3])
        );
    } elseif (count($parts) >= 2) {
        // 旧格式兼容：城市名|时区
        $clockCities[] = array(
            'city' => trim($parts[0]),
            'lat' => 0, 'lon' => 0,
            'timezone' => trim($parts[1])
        );
    }
}

// 注入天气城市数据
$weatherCitiesRaw = nav17_get_theme_config('weatherCities', "北京|39.9|116.4\n上海|31.2|121.5");
$weatherCities = array();
foreach (explode("\n", $weatherCitiesRaw) as $line) {
    $line = trim($line);
    if (empty($line)) continue;
    $parts = explode('|', $line);
    if (count($parts) >= 3) {
        $weatherCities[] = array(
            'city' => trim($parts[0]),
            'lat' => floatval(trim($parts[1])),
            'lon' => floatval(trim($parts[2]))
        );
    }
}
// 注入天气显示选项
$weatherOptsRaw = nav17_get_theme_config('weatherOptions', array('tempRange','weatherType','humidity','sunrise'));
if (!is_array($weatherOptsRaw)) {
    $weatherOptsRaw = explode(',', $weatherOptsRaw);
}
$weatherOpts = array();
foreach ($weatherOptsRaw as $opt) {
    if (is_array($opt)) $weatherOpts[] = $opt[0];
    else $weatherOpts[] = $opt;
}
?>
<script>
    window.NAV_CLOCK_CITIES = <?php echo json_encode($clockCities); ?>;
    window.NAV_WEATHER_CITIES = <?php echo json_encode($weatherCities); ?>;
    window.NAV_WEATHER_OPTIONS = <?php echo json_encode($weatherOpts); ?>;
    window.NAV_BOOKMARKS = [];
    window.NAV_CATEGORIES = [];
</script>
<script src="<?php $this->options->themeUrl('assets/js/theme.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('assets/js/clock.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('assets/js/weather.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('assets/js/search.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('assets/js/graph.js'); ?>"></script>
</body>
</html>
