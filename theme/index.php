<?php
/**
 * 17Nav 导航站主题
 *
 * @package 17Nav
 * @author  Skyline
 * @version 1.0.0
 * @link    https://17ai.icu
 * @license GPL-3.0
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$this->need('header.php');
?>

<main class="main-wrap">
    <div class="container">
        <!-- 搜索栏 -->
        <?php $this->need('search.php'); ?>

        <!-- 右侧组件 -->
        <div class="widgets">
            <?php $this->need('clock.php'); ?>
            <?php $this->need('weather.php'); ?>
        </div>

        <!-- 书签卡片网格 -->
        <div class="bookmark-grid" id="bookmark-grid">
            <?php $this->need('sidebar.php'); ?>
        </div>

        <!-- 标签云 + 知识图谱入口 -->
        <div class="bottom-bar">
            <div class="tag-cloud" id="tag-cloud"></div>
            <button class="graph-btn" id="graph-btn">🕸 知识图谱</button>
        </div>
    </div>
</main>

<?php $this->need('footer.php'); ?>
