<?php
/**
 * 17Nav 导航管理主页
 */
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body container">
        <div class="typecho-page-title">
            <h2>导航管理</h2>
        </div>
        <div class="row">
            <div class="col-mb-12">
                <div class="typecho-list-operate">
                    <a href="edit.php" class="btn btn-primary">+ 添加书签</a>
                </div>
                <div class="typecho-table-wrap">
                    <table class="typecho-list-table">
                        <thead>
                            <tr>
                                <th>名称</th>
                                <th>URL</th>
                                <th>分类</th>
                                <th>标签</th>
                                <th>点击</th>
                                <th>权重</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody id="bookmark-table-body">
                            <!-- JS 动态渲染 -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 加载书签列表
fetch('/index.php/17nav/api?do=list')
    .then(r => r.json())
    .then(data => {
        const tbody = document.getElementById('bookmark-table-body');
        tbody.innerHTML = data.map(b => `
            <tr>
                <td>${b.name}</td>
                <td><a href="${b.url}" target="_blank">${b.url}</a></td>
                <td>${b.category || '-'}</td>
                <td>${(b.tags || []).join(', ')}</td>
                <td>${b.clicks}</td>
                <td>${b.weight}</td>
                <td>
                    <a href="edit.php?cid=${b.cid}">编辑</a> |
                    <a href="#" onclick="del(${b.cid})">删除</a>
                </td>
            </tr>
        `).join('');
    });
</script>

<?php include 'footer.php'; ?>
