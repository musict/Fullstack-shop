<?php


namespace core\admin\controller;


use core\base\settings\Settings;

class AddController extends BaseAdmin
{
    protected $action = 'add';

    protected function inputData()
    {
        if (!$this->userId) $this->execBase();
        $this->checkPost();
        $this->createTableData();
        $this->createForeignData();
        $this->createMenuPosition();
        $this->createRadio();
        $this->createOutputData();
        $this->createManyToMany();
        return $this->expansion();
    }

}