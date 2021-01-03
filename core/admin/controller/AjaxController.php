<?php


namespace core\admin\controller;


use core\base\controller\BaseAjax;

class AjaxController extends BaseAjax
{
    public function ajax(){
        if (isset($this->data['ajax'])){
            switch ($this->data['ajax']){
                case 'sitemap':
                    return (new CreatesitemapController())->inputData($this->data['links_counter'], false);
            }
        }
        return json_encode(['success' => '0', 'message' => 'No ajax variable']);

    }
}