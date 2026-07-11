<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 17Nav AJAX 接口
 *
 * @package Nav17Manager
 * @license AGPL-3.0
 */
class Nav17Manager_Action extends Typecho_Widget implements Widget_Interface_Do
{
    public function action()
    {
        $this->response->setContentType('application/json');

        $action = $this->request->get('do', '');

        switch ($action) {
            case 'list':
                $this->listBookmarks();
                break;
            case 'add':
                $this->addBookmark();
                break;
            case 'edit':
                $this->editBookmark();
                break;
            case 'delete':
                $this->deleteBookmark();
                break;
            default:
                $this->response->throwJson(array('error' => 'unknown action'));
        }
    }

    /**
     * 点击统计
     */
    public function click()
    {
        $this->response->setContentType('application/json');

        $url = $this->request->get('url', '');
        if (!$url) {
            $this->response->throwJson(array('error' => 'no url'));
        }

        $db = Typecho_Db::get();
        $post = $db->fetchRow($db->select('cid')
            ->from('table.contents')
            ->where('type = ?', 'post')
            ->where('status = ?', 'publish'));

        // 通过自定义字段查找
        $field = $db->fetchRow($db->select('cid', 'str_value')
            ->from('table.fields')
            ->where('name = ?', 'nav_url')
            ->where('str_value = ?', $url));

        if ($field) {
            $clicks = intval($field['str_value']) + 1;
            $db->query($db->update('table.fields')
                ->rows(array('int_value' => $clicks))
                ->where('cid = ?', $field['cid'])
                ->where('name = ?', 'nav_clicks'));
        }

        $this->response->throwJson(array('ok' => true));
    }

    private function listBookmarks()
    {
        $bookmarks = Nav17Manager_Plugin::getBookmarks();
        $this->response->throwJson($bookmarks);
    }

    private function addBookmark()
    {
        // TODO: 创建文章 + 自定义字段
        $this->response->throwJson(array('ok' => true));
    }

    private function editBookmark()
    {
        // TODO: 更新文章 + 自定义字段
        $this->response->throwJson(array('ok' => true));
    }

    private function deleteBookmark()
    {
        // TODO: 删除文章
        $this->response->throwJson(array('ok' => true));
    }
}
