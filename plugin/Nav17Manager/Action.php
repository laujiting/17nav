<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 17Nav AJAX 接口
 *
 * @package Nav17Manager
 * @license AGPL-3.0
 */
class Nav17Manager_Action extends \Typecho\Widget implements \Typecho\Widget\ActionInterface
{
    public function action()
    {
        $this->response->setContentType('application/json');

        // 验证登录
        if (!\Typecho\Widget::widget('Widget_User')->hasLogin()) {
            $this->response->throwJson(array('error' => '请先登录'));
        }

        $action = $this->request->get('do', '');

        switch ($action) {
            case 'list':
                $this->listBookmarks();
                break;
            case 'categories':
                $this->listCategories();
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
     * 点击统计（不需要登录）
     */
    public function click()
    {
        $this->response->setContentType('application/json');

        $url = $this->request->get('url', '');
        if (!$url) {
            $this->response->throwJson(array('error' => 'no url'));
        }

        $db = \Typecho\Db::get();
        $field = $db->fetchRow($db->select('cid', 'int_value')
            ->from('table.fields')
            ->where('name = ?', 'nav_url')
            ->where('str_value = ?', $url));

        if ($field) {
            $clicks = intval($field['int_value']) + 1;
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

    private function listCategories()
    {
        $categories = Nav17Manager_Plugin::getCategories();
        $this->response->throwJson($categories);
    }

    private function addBookmark()
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (empty($data['name']) || empty($data['url'])) {
            $this->response->throwJson(array('error' => '名称和 URL 为必填项'));
        }

        try {
            $cid = Nav17Manager_Plugin::addBookmark($data);
            $this->response->throwJson(array('ok' => true, 'cid' => $cid));
        } catch (\Exception $e) {
            $this->response->throwJson(array('error' => $e->getMessage()));
        }
    }

    private function editBookmark()
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (empty($data['cid'])) {
            $this->response->throwJson(array('error' => '缺少书签 ID'));
        }

        try {
            Nav17Manager_Plugin::editBookmark($data['cid'], $data);
            $this->response->throwJson(array('ok' => true));
        } catch (\Exception $e) {
            $this->response->throwJson(array('error' => $e->getMessage()));
        }
    }

    private function deleteBookmark()
    {
        $cid = intval($this->request->get('cid', 0));
        if (!$cid) {
            $this->response->throwJson(array('error' => '缺少书签 ID'));
        }

        try {
            Nav17Manager_Plugin::deleteBookmark($cid);
            $this->response->throwJson(array('ok' => true));
        } catch (\Exception $e) {
            $this->response->throwJson(array('error' => $e->getMessage()));
        }
    }
}
