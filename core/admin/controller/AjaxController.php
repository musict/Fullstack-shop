<?php

namespace core\admin\controller;

class AjaxController extends BaseAdmin
{
    public function ajax(){
        if (isset($this->data['ajax'])){

            $this->execBase();

            switch ($this->data['ajax']){
                case 'sitemap':
                    return (new CreatesitemapController())->inputData($this->data['links_counter'], false);
                case 'editData':
                    $_POST['return_id'] = true;
                    $this->checkPost();
                    return json_encode(['success' => 1]);
            }
        }
        return json_encode(['success' => '0', 'message' => 'No ajax variable']);

    }
}