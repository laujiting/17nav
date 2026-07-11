<?php
/**
 * 17Nav 主题函数
 *
 * @package 17Nav
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 主题配置
 */
function themeConfig($form)
{
    // 主题模式
    $themeMode = new Typecho_Widget_Helper_Form_Element_Radio(
        'themeMode',
        array('auto' => '跟随系统', 'dark' => '暗色', 'light' => '亮色'),
        'auto',
        '主题模式',
        '选择默认主题模式'
    );
    $form->addInput($themeMode);

    // 背景图片
    $bgImage = new Typecho_Widget_Helper_Form_Element_Text(
        'bgImage', null, '', '背景图片 URL', '留空则使用纯色背景'
    );
    $form->addInput($bgImage);

    // 背景模糊
    $bgBlur = new Typecho_Widget_Helper_Form_Element_Text(
        'bgBlur', null, '0', '背景模糊度 (px)', '0-20，0 为不模糊'
    );
    $form->addInput($bgBlur);

    // 遮罩透明度
    $bgOpacity = new Typecho_Widget_Helper_Form_Element_Text(
        'bgOpacity', null, '0', '遮罩透明度 (%)', '0-100，0 为无遮罩'
    );
    $form->addInput($bgOpacity);

    // 背景位置
    $bgPosition = new Typecho_Widget_Helper_Form_Element_Select(
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
    $bgSize = new Typecho_Widget_Helper_Form_Element_Select(
        'bgSize',
        array('cover' => '填满裁切', 'contain' => '完整显示', '100% 100%' => '拉伸'),
        'cover', '背景缩放模式'
    );
    $form->addInput($bgSize);

    // 背景缩放比例
    $bgScale = new Typecho_Widget_Helper_Form_Element_Text(
        'bgScale', null, '100', '背景缩放比例 (%)', '50-200，100 为原始大小'
    );
    $form->addInput($bgScale);

    // 时钟城市
    $clockCities = new Typecho_Widget_Helper_Form_Element_Textarea(
        'clockCities', null,
        "北京|Asia/Shanghai\n纽约|America/New_York\n伦敦|Europe/London",
        '世界时钟城市', '每行一个，格式：城市名|时区'
    );
    $form->addInput($clockCities);

    // 天气城市
    $weatherCities = new Typecho_Widget_Helper_Form_Element_Textarea(
        'weatherCities', null,
        "北京|39.9|116.4\n上海|31.2|121.5",
        '天气城市', '每行一个，格式：城市名|纬度|经度'
    );
    $form->addInput($weatherCities);
}
