<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 注入时钟城市数据
$clockCitiesRaw = nav17_get_theme_config('clockCities', "北京|Asia/Shanghai\n纽约|America/New_York\n伦敦|Europe/London");
$clockCities = array();
foreach (explode("\n", $clockCitiesRaw) as $line) {
    $line = trim($line);
    if (empty($line)) continue;
    $parts = explode('|', $line);
    if (count($parts) >= 2) {
        $clockCities[] = array('city' => trim($parts[0]), 'timezone' => trim($parts[1]));
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
?>
<script>
    window.NAV_CLOCK_CITIES = <?php echo json_encode($clockCities); ?>;
    window.NAV_WEATHER_CITIES = <?php echo json_encode($weatherCities); ?>;
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
