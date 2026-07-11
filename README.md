# 17nav - Typecho 导航站主题 + 管理插件

基于 Typecho 的现代化导航站解决方案，包含自定义主题和独立后台管理插件。

## 特性

- 🎨 **PWOS 风格设计**：深色渐变侧边栏 + 卡片网格布局，暗/亮/自动三种主题
- 🔍 **智能搜索**：站内悬浮搜索 + 多引擎跳转（百度/Google/Bing/GitHub）
- 🕐 **世界时钟**：多城市时区显示，前端实时计算
- 🌤 **天气组件**：多地区天气，Open-Meteo 免费数据源
- 🗂 **多标签系统**：预设标签（#年月/分类）+ 自定义标签 + 标签云
- 🕸 **知识图谱**：D3.js 力导向图，书签聚类可视化
- 🖼 **背景自定义**：图片上传 + 模糊/位置/裁切调整 + 实时预览
- 📱 **移动端适配**：响应式布局，触摸手势支持
- ✨ **动画系统**：微交互 + 场景过渡 + 数据可视化动画
- 🔐 **Authelia 兼容**：支持 SSO 集成

## 技术栈

- **后端**：PHP 8.2+ / Typecho 1.3.0+
- **前端**：原生 CSS + ES6，D3.js（知识图谱）
- **数据库**：SQLite（Typecho 自带）
- **许可证**：GPL-3.0

## 目录结构

```
17nav/
├── theme/                  # Typecho 主题
│   ├── index.php           # 首页模板
│   ├── header.php          # 公共头部
│   ├── footer.php          # 公共尾部
│   ├── sidebar.php         # 左侧分类栏
│   ├── functions.php       # 主题函数 + themeConfig
│   └── assets/
│       ├── css/
│       │   ├── main.css       # 主样式（亮色）
│       │   ├── dark.css       # 暗色模式
│       │   └── responsive.css # 移动端适配
│       ├── js/
│       │   ├── search.js      # 搜索 + 悬浮结果
│       │   ├── clock.js       # 世界时钟
│       │   ├── weather.js     # 天气获取
│       │   ├── graph.js       # 知识图谱
│       │   ├── background.js  # 背景自定义
│       │   └── theme.js       # 主题切换 + 动画
│       └── img/               # 预置图标 + 背景预设
│
├── plugin/                 # Typecho 插件
│   ├── Plugin.php          # 插件入口
│   ├── Action.php          # AJAX 接口
│   └── admin/
│       ├── manage.php      # 导航管理主页
│       ├── edit.php        # 添加/编辑书签
│       └── settings.php    # 全局设置
│
├── dev/                    # 开发工具
│   └── deploy.sh           # 部署脚本（scp 到 ECS）
│
└── docs/                   # 文档
    └── requirements.md     # 完整需求规格
```

## 安装

### 部署到 Typecho

1. 将 `theme/` 目录上传到 Typecho 的 `usr/themes/17nav/`
2. 将 `plugin/` 目录上传到 Typecho 的 `usr/plugins/17NavManager/`
3. 在 Typecho 后台启用 17nav 主题
4. 在 Typecho 后台启用 17NavManager 插件
5. 在插件设置中配置时钟城市、天气城市等

### ECS 部署

```bash
# 使用 deploy.sh 一键部署
bash dev/deploy.sh
```

## 开发

本地开发，ECS 验证。修改 `theme/` 和 `plugin/` 后运行 `dev/deploy.sh` 同步到 ECS。

## 许可证

GPL-3.0 - 详见 [LICENSE](LICENSE)

商业许可请联系作者。
