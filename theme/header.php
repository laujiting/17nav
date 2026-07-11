<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$navConfig = $this->options->plugin('17Nav');
$themeMode = $navConfig ? $navConfig->themeMode : 'auto';
$bgImage = $navConfig ? $navConfig->bgImage : '';
$bgBlur = $navConfig ? $navConfig->bgBlur : '0';
$bgOpacity = $navConfig ? $navConfig->bgOpacity : '0';
$bgPosition = $navConfig ? $navConfig->bgPosition : 'center';
$bgSize = $navConfig ? $navConfig->bgSize : 'cover';
$bgScale = $navConfig ? $navConfig->bgScale : '100';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php $this->options->title(); ?></title>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/main.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/dark.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/responsive.css'); ?>">
    <script>
        window.NAV_CONFIG = {
            themeMode: '<?php echo $themeMode; ?>',
            bgImage: '<?php echo $bgImage; ?>',
            bgBlur: '<?php echo $bgBlur; ?>',
            bgOpacity: '<?php echo $bgOpacity; ?>',
            bgPosition: '<?php echo $bgPosition; ?>',
            bgSize: '<?php echo $bgSize; ?>',
            bgScale: '<?php echo $bgScale; ?>'
        };
    </script>
</head>
<body data-theme="<?php echo $themeMode; ?>">
    <div class="background-layer" id="bg-layer"></div>
    <div class="background-overlay" id="bg-overlay"></div>
