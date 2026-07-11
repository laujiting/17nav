<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
include 'header.php';
include 'menu.php';
?>
<div class="main">
    <div class="body container">
        <div class="typecho-page-title">
            <h2>导航管理 <a href="edit.php" class="btn btn-primary" style="float:right">+ 添加书签</a></h2>
        </div>
        <div class="row">
            <div class="col-mb-12">
                <div class="typecho-table-wrap">
                    <table class="typecho-list-table" style="width:100%">
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
                            <tr><td colspan="7" style="text-align:center;padding:2rem">加载中...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
fetch('/index.php/nav17/api?do=list')
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var tbody = document.getElementById('bookmark-table-body');
        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:#999">暂无书签，点击右上角添加</td></tr>';
            return;
        }
        tbody.innerHTML = data.map(function(b) {
            return '<tr>' +
                '<td>' + escapeHtml(b.name) + '</td>' +
                '<td><a href="' + escapeHtml(b.url) + '" target="_blank" style="color:#3730a3">' + escapeHtml(b.url) + '</a></td>' +
                '<td>' + escapeHtml(b.category || '-') + '</td>' +
                '<td>' + (b.tags || []).map(escapeHtml).join(', ') + '</td>' +
                '<td>' + b.clicks + '</td>' +
                '<td>' + b.weight + '</td>' +
                '<td><a href="edit.php?cid=' + b.cid + '">编辑</a> | <a href="#" onclick="del(' + b.cid + ');return false;" style="color:#b91c1c">删除</a></td>' +
                '</tr>';
        }).join('');
    })
    .catch(function(e) {
        document.getElementById('bookmark-table-body').innerHTML =
            '<tr><td colspan="7" style="text-align:center;padding:2rem;color:#b91c1c">加载失败: ' + e.message + '</td></tr>';
    });

function escapeHtml(s) {
    var d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
}

function del(cid) {
    if (!confirm('确定删除这个书签吗？')) return;
    fetch('/index.php/nav17/api?do=delete&cid=' + cid)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.ok) {
                location.reload();
            } else {
                alert('删除失败: ' + (data.error || '未知错误'));
            }
        });
}
</script>
<?php include 'footer.php'; ?>
