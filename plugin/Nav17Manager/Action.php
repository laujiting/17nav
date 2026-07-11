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

    private function addBookmark()
    {
        $this->response->throwJson(array('ok' => true));
    }

    private function editBookmark()
    {
        $this->response->throwJson(array('ok' => true));
    }

    private function deleteBookmark()
    {
        $this->response->throwJson(array('ok' => true));
    }
}
