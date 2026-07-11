<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2><?php $this->options->title(); ?></h2>
        <span class="ver">17Nav v1.0</span>
    </div>
    <nav class="sidebar-nav">
        <?php $this->widget('Widget_Metas_Category_List')->to($categories); ?>
        <div class="sidebar-group">
            <div class="sidebar-group-title">分类</div>
            <a href="#" class="active" data-category="all">全部</a>
            <?php while ($categories->next()): ?>
                <a href="#<?php $categories->slug(); ?>" data-category="<?php $categories->slug(); ?>">
                    <?php $categories->name(); ?>
                </a>
            <?php endwhile; ?>
        </div>
    </nav>
</aside>

<!-- 分类内容区：书签卡片 -->
<section class="bookmark-section" id="bookmark-section">
    <!-- 卡片由 JS 动态渲染 -->
</section>
