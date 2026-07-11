<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 17Nav 导航管理插件
 *
 * @package Nav17Manager
 * @author  Skyline
 * @version 1.0.0
 * @link    https://17ai.icu
 * @license AGPL-3.0
 */
class Nav17Manager_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        // 注册后台菜单
        Helper::addPanel(1, 'Nav17Manager/admin/manage.php', '导航管理', '导航管理', 'administrator');

        // 注册 AJAX 路由
        Helper::addRoute('nav17-api', '/nav17/api', 'Nav17Manager_Action', 'action');
        Helper::addRoute('nav17-click', '/nav17/click', 'Nav17Manager_Action', 'click');

        return _t('17Nav 导航管理插件已启用');
    }

    public static function deactivate()
    {
        Helper::removePanel(1, 'Nav17Manager/admin/manage.php');
        Helper::removeRoute('nav17-api');
        Helper::removeRoute('nav17-click');
    }

    public static function config($form)
    {
        $sortRule = new Typecho_Widget_Helper_Form_Element_Select(
            'sortRule',
            array(
                'weight' => '按权重',
                'clicks' => '按点击频次',
                'name' => '按名称序'
            ),
            'weight',
            '默认排序规则',
            '书签的默认排序方式'
        );
        $form->addInput($sortRule);

        $presetTags = new Typecho_Widget_Helper_Form_Element_Textarea(
            'presetTags', null,
            "#2026-07\n#AI\n#开发\n#工具\n#学习\n#运维\n#设计\n#娱乐\n#社交",
            '预设标签', '每行一个，以 # 开头'
        );
        $form->addInput($presetTags);
    }

    public static function personalConfig($form) {}

    /**
     * 获取所有书签
     */
    public static function getBookmarks()
    {
        $db = Typecho_Db::get();
        $posts = $db->fetchAll($db->select()
            ->from('table.contents')
            ->where('type = ?', 'post')
            ->where('status = ?', 'publish')
            ->order('order', Typecho_Db::SORT_ASC));

        $bookmarks = array();
        foreach ($posts as $post) {
            $fields = $db->fetchAll($db->select()
                ->from('table.fields')
                ->where('cid = ?', $post['cid']));

            $fieldMap = array();
            foreach ($fields as $f) {
                $fieldMap[$f['name']] = $f['str_value'];
            }

            $bookmarks[] = array(
                'cid' => $post['cid'],
                'name' => $post['title'],
                'url' => $fieldMap['nav_url'] ?? '',
                'icon' => $fieldMap['nav_icon'] ?? '',
                'desc' => $post['text'] ?? '',
                'weight' => intval($fieldMap['nav_weight'] ?? 0),
                'clicks' => intval($fieldMap['nav_clicks'] ?? 0),
            );
        }

        return $bookmarks;
    }
}
