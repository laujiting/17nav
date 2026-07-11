<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
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
                <form id="bookmark-form" style="max-width:600px">
                    <input type="hidden" name="cid" id="cid" value="">
                    <p>
                        <label style="display:block;margin-bottom:0.3rem;font-weight:600">名称 *</label>
                        <input type="text" name="name" id="name" required
                            style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:0.5rem">
                    </p>
                    <p>
                        <label style="display:block;margin-bottom:0.3rem;font-weight:600">URL *</label>
                        <input type="url" name="url" id="url" required placeholder="https://"
                            style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:0.5rem">
                    </p>
                    <p>
                        <label style="display:block;margin-bottom:0.3rem;font-weight:600">分类</label>
                        <select name="categoryId" id="categoryId"
                            style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:0.5rem">
                            <option value="">选择分类</option>
                        </select>
                    </p>
                    <p>
                        <label style="display:block;margin-bottom:0.3rem;font-weight:600">描述</label>
                        <textarea name="desc" id="desc" rows="3" placeholder="悬浮显示的描述文字"
                            style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:0.5rem"></textarea>
                    </p>
                    <p>
                        <label style="display:block;margin-bottom:0.3rem;font-weight:600">标签</label>
                        <input type="text" name="tags" id="tags" placeholder="多个标签用逗号分隔"
                            style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:0.5rem">
                    </p>
                    <p>
                        <label style="display:block;margin-bottom:0.3rem;font-weight:600">排序权重</label>
                        <input type="number" name="weight" id="weight" value="0"
                            style="width:100px;padding:0.5rem;border:1px solid #ddd;border-radius:0.5rem">
                    </p>
                    <p>
                        <label style="display:block;margin-bottom:0.3rem;font-weight:600">图标 URL（留空自动获取）</label>
                        <input type="url" name="icon" id="icon" placeholder="自动获取 favicon"
                            style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:0.5rem">
                    </p>
                    <p style="margin-top:1rem">
                        <button type="submit" class="btn btn-primary">保存</button>
                        <a href="manage.php" class="btn" style="margin-left:0.5rem">取消</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
// 加载分类列表
fetch('/index.php/nav17/api?do=categories')
    .then(function(r) { return r.json(); })
    .then(function(cats) {
        var sel = document.getElementById('categoryId');
        cats.forEach(function(c) {
            var opt = document.createElement('option');
            opt.value = c.mid;
            opt.textContent = c.name;
            sel.appendChild(opt);
        });
    });

// 如果是编辑，加载书签数据
var cid = new URLSearchParams(location.search).get('cid');
if (cid) {
    document.getElementById('cid').value = cid;
    fetch('/index.php/nav17/api?do=list')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var b = data.find(function(x) { return x.cid == cid; });
            if (b) {
                document.getElementById('name').value = b.name;
                document.getElementById('url').value = b.url;
                document.getElementById('desc').value = b.desc;
                document.getElementById('tags').value = (b.tags || []).join(', ');
                document.getElementById('weight').value = b.weight;
                document.getElementById('icon').value = b.icon;
            }
        });
}

// 提交表单
document.getElementById('bookmark-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var tagsStr = document.getElementById('tags').value;
    var data = {
        cid: document.getElementById('cid').value || null,
        name: document.getElementById('name').value,
        url: document.getElementById('url').value,
        categoryId: document.getElementById('categoryId').value,
        desc: document.getElementById('desc').value,
        tags: tagsStr ? tagsStr.split(',').map(function(t) { return t.trim(); }).filter(Boolean) : [],
        weight: parseInt(document.getElementById('weight').value) || 0,
        icon: document.getElementById('icon').value
    };

    var action = data.cid ? 'edit' : 'add';
    fetch('/index.php/nav17/api?do=' + action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(function(r) { return r.json(); })
    .then(function(result) {
        if (result.ok) {
            window.location.href = 'manage.php';
        } else {
            alert('保存失败: ' + (result.error || '未知错误'));
        }
    })
    .catch(function(e) {
        alert('请求失败: ' + e.message);
    });
});
</script>
<?php include 'footer.php'; ?>
