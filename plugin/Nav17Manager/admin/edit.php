<?php
/**
 * 17Nav 添加/编辑书签
 */
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body container">
        <div class="typecho-page-title">
            <h2><?php echo isset($_GET['cid']) ? '编辑书签' : '添加书签'; ?></h2>
        </div>
        <div class="row">
            <div class="col-mb-12">
                <form id="bookmark-form" class="typecho-form">
                    <input type="hidden" name="cid" value="<?php echo $_GET['cid'] ?? ''; ?>">
                    <p>
                        <label>名称 *</label>
                        <input type="text" name="name" required>
                    </p>
                    <p>
                        <label>URL *</label>
                        <input type="url" name="url" required placeholder="https://">
                    </p>
                    <p>
                        <label>分类 *</label>
                        <select name="category" required>
                            <option value="">选择分类</option>
                        </select>
                    </p>
                    <p>
                        <label>描述</label>
                        <textarea name="desc" rows="3" placeholder="悬浮显示的描述文字"></textarea>
                    </p>
                    <p>
                        <label>标签</label>
                        <input type="text" name="tags" placeholder="多个标签用逗号分隔">
                    </p>
                    <p>
                        <label>排序权重</label>
                        <input type="number" name="weight" value="0">
                    </p>
                    <p>
                        <label>图标 URL</label>
                        <input type="url" name="icon" placeholder="留空自动获取 favicon">
                    </p>
                    <p class="submit">
                        <button type="submit" class="btn btn-primary">保存</button>
                        <a href="manage.php" class="btn">取消</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('bookmark-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const data = {
        cid: form.cid.value,
        name: form.name.value,
        url: form.url.value,
        category: form.category.value,
        desc: form.desc.value,
        tags: form.tags.value.split(',').map(t => t.trim()).filter(Boolean),
        weight: parseInt(form.weight.value) || 0,
        icon: form.icon.value
    };

    const action = data.cid ? 'edit' : 'add';
    const res = await fetch('/index.php/nav17/api?do=' + action, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.ok) {
        window.location.href = 'manage.php';
    } else {
        alert('保存失败: ' + (result.error || '未知错误'));
    }
});
</script>

<?php include 'footer.php'; ?>
