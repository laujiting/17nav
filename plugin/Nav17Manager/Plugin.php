<?php

namespace TypechoPlugin\Nav17Manager;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Select;
use Typecho\Widget\Helper\Form\Element\Textarea;
use Typecho\Db;

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
class Plugin implements PluginInterface
{
    public static function activate()
    {
        \Helper::addPanel(1, 'Nav17Manager/admin/manage.php', '导航管理', '导航管理', 'administrator');
        \Helper::addRoute('nav17-api', '/nav17/api', 'Nav17Manager_Action', 'action');
        \Helper::addRoute('nav17-click', '/nav17/click', 'Nav17Manager_Action', 'click');
        return _t('17Nav 导航管理插件已启用');
    }

    public static function deactivate()
    {
        \Helper::removePanel(1, 'Nav17Manager/admin/manage.php');
        \Helper::removeRoute('nav17-api');
        \Helper::removeRoute('nav17-click');
    }

    public static function config(Form $form)
    {
        $sortRule = new Select(
            'sortRule',
            array('weight' => '按权重', 'clicks' => '按点击频次', 'name' => '按名称序'),
            'weight', '默认排序规则', '书签的默认排序方式'
        );
        $form->addInput($sortRule);

        $presetTags = new Textarea(
            'presetTags', null,
            "#2026-07\n#AI\n#开发\n#工具\n#学习\n#运维\n#设计\n#娱乐\n#社交",
            '预设标签', '每行一个，以 # 开头'
        );
        $form->addInput($presetTags);
    }

    public static function personalConfig(Form $form) {}

    /**
     * 获取所有书签
     */
    public static function getBookmarks()
    {
        $db = Db::get();
        $posts = $db->fetchAll($db->select()
            ->from('table.contents')
            ->where('type = ?', 'post')
            ->where('status = ?', 'publish')
            ->order('order', Db::SORT_ASC));

        $bookmarks = array();
        foreach ($posts as $post) {
            $fields = $db->fetchAll($db->select()
                ->from('table.fields')
                ->where('cid = ?', $post['cid']));

            $fieldMap = array();
            foreach ($fields as $f) {
                $fieldMap[$f['name']] = $f['str_value'] ?? $f['int_value'] ?? '';
            }

            // 获取分类
            $metas = $db->fetchAll($db->select()
                ->from('table.metas')
                ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
                ->where('table.relationships.cid = ?', $post['cid'])
                ->where('table.metas.type = ?', 'category'));
            $category = !empty($metas) ? $metas[0]['name'] : '';
            $categorySlug = !empty($metas) ? $metas[0]['slug'] : '';

            // 获取标签
            $tags = $db->fetchAll($db->select()
                ->from('table.metas')
                ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
                ->where('table.relationships.cid = ?', $post['cid'])
                ->where('table.metas.type = ?', 'tag'));
            $tagList = array_map(function($t) { return $t['name']; }, $tags);

            $text = $post['text'] ?? '';
            $desc = preg_replace('/^<!--markdown-->/', '', $text);
            $desc = trim(strip_tags($desc));

            $bookmarks[] = array(
                'cid' => $post['cid'],
                'name' => $post['title'],
                'url' => $fieldMap['nav_url'] ?? '',
                'icon' => $fieldMap['nav_icon'] ?? '',
                'desc' => mb_substr($desc, 0, 100),
                'category' => $category,
                'categorySlug' => $categorySlug,
                'tags' => $tagList,
                'weight' => intval($fieldMap['nav_weight'] ?? 0),
                'clicks' => intval($fieldMap['nav_clicks'] ?? 0),
            );
        }

        return $bookmarks;
    }

    /**
     * 获取所有分类
     */
    public static function getCategories()
    {
        $db = Db::get();
        $metas = $db->fetchAll($db->select()
            ->from('table.metas')
            ->where('type = ?', 'category')
            ->order('order', Db::SORT_ASC));
        return array_map(function($m) {
            return array('mid' => $m['mid'], 'name' => $m['name'], 'slug' => $m['slug']);
        }, $metas);
    }

    /**
     * 添加书签
     */
    public static function addBookmark($data)
    {
        $db = Db::get();

        $icon = $data['icon'] ?? '';
        if (empty($icon) && !empty($data['url'])) {
            $domain = parse_url($data['url'], PHP_URL_HOST);
            if ($domain) {
                $icon = 'https://www.google.com/s2/favicons?domain=' . $domain . '&sz=64';
            }
        }

        $now = time();

        $cid = $db->query($db->insert('table.contents')
            ->rows(array(
                'title' => $data['name'],
                'slug' => \Typecho\Common::slugName($data['name']),
                'created' => $now,
                'modified' => $now,
                'text' => '<!--markdown-->' . ($data['desc'] ?? ''),
                'order' => 0,
                'authorId' => 1,
                'template' => '',
                'type' => 'post',
                'status' => 'publish',
                'password' => '',
                'commentsNum' => 0,
                'allowComment' => '0',
                'allowPing' => '0',
                'allowFeed' => '0',
                'parent' => 0,
            )));

        $fields = array(
            'nav_url' => $data['url'] ?? '',
            'nav_icon' => $icon,
            'nav_weight' => strval($data['weight'] ?? 0),
            'nav_clicks' => '0',
        );

        foreach ($fields as $name => $value) {
            $db->query($db->insert('table.fields')
                ->rows(array(
                    'cid' => $cid,
                    'name' => $name,
                    'type' => 'str',
                    'str_value' => $value,
                    'int_value' => 0,
                )));
        }

        if (!empty($data['categoryId'])) {
            $db->query($db->insert('table.relationships')
                ->rows(array('cid' => $cid, 'mid' => intval($data['categoryId']))));
        }

        if (!empty($data['tags'])) {
            foreach ($data['tags'] as $tagName) {
                $tagName = trim($tagName);
                if (empty($tagName)) continue;

                $existing = $db->fetchRow($db->select('mid')
                    ->from('table.metas')
                    ->where('type = ?', 'tag')
                    ->where('name = ?', $tagName));

                if ($existing) {
                    $mid = $existing['mid'];
                } else {
                    $mid = $db->query($db->insert('table.metas')
                        ->rows(array(
                            'name' => $tagName,
                            'slug' => \Typecho\Common::slugName($tagName),
                            'type' => 'tag',
                            'description' => '',
                            'count' => 0,
                            'order' => 0,
                            'parent' => 0,
                        )));
                }

                $db->query($db->insert('table.relationships')
                    ->rows(array('cid' => $cid, 'mid' => $mid)));
            }
        }

        return $cid;
    }

    /**
     * 编辑书签
     */
    public static function editBookmark($cid, $data)
    {
        $db = Db::get();

        $icon = $data['icon'] ?? '';
        if (empty($icon) && !empty($data['url'])) {
            $domain = parse_url($data['url'], PHP_URL_HOST);
            if ($domain) {
                $icon = 'https://www.google.com/s2/favicons?domain=' . $domain . '&sz=64';
            }
        }

        $db->query($db->update('table.contents')
            ->rows(array(
                'title' => $data['name'],
                'text' => '<!--markdown-->' . ($data['desc'] ?? ''),
                'modified' => time(),
            ))
            ->where('cid = ?', $cid));

        $fields = array(
            'nav_url' => $data['url'] ?? '',
            'nav_icon' => $icon,
            'nav_weight' => strval($data['weight'] ?? 0),
        );

        foreach ($fields as $name => $value) {
            $existing = $db->fetchRow($db->select('cid')
                ->from('table.fields')
                ->where('cid = ?', $cid)
                ->where('name = ?', $name));

            if ($existing) {
                $db->query($db->update('table.fields')
                    ->rows(array('str_value' => $value))
                    ->where('cid = ?', $cid)
                    ->where('name = ?', $name));
            } else {
                $db->query($db->insert('table.fields')
                    ->rows(array(
                        'cid' => $cid,
                        'name' => $name,
                        'type' => 'str',
                        'str_value' => $value,
                        'int_value' => 0,
                    )));
            }
        }

        // 更新分类关联
        if (!empty($data['categoryId'])) {
            $db->query($db->delete('table.relationships')
                ->where('cid = ?', $cid));

            $db->query($db->insert('table.relationships')
                ->rows(array('cid' => $cid, 'mid' => intval($data['categoryId']))));

            if (!empty($data['tags'])) {
                foreach ($data['tags'] as $tagName) {
                    $tagName = trim($tagName);
                    if (empty($tagName)) continue;

                    $existing = $db->fetchRow($db->select('mid')
                        ->from('table.metas')
                        ->where('type = ?', 'tag')
                        ->where('name = ?', $tagName));

                    if ($existing) {
                        $mid = $existing['mid'];
                    } else {
                        $mid = $db->query($db->insert('table.metas')
                            ->rows(array(
                                'name' => $tagName,
                                'slug' => \Typecho\Common::slugName($tagName),
                                'type' => 'tag',
                                'description' => '',
                                'count' => 0,
                                'order' => 0,
                                'parent' => 0,
                            )));
                    }

                    $db->query($db->insert('table.relationships')
                        ->rows(array('cid' => $cid, 'mid' => $mid)));
                }
            }
        }

        return true;
    }

    /**
     * 删除书签
     */
    public static function deleteBookmark($cid)
    {
        $db = Db::get();
        $db->query($db->delete('table.contents')->where('cid = ?', $cid));
        $db->query($db->delete('table.fields')->where('cid = ?', $cid));
        $db->query($db->delete('table.relationships')->where('cid = ?', $cid));
        return true;
    }
}
