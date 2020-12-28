<?php


namespace core\admin\controller;


class EditController extends BaseAdmin
{
    protected function inputData(){
        if (!$this->userId) $this->execBase();
    }

    protected function checkOldAlias($id){
        $tables = $this->model->showTables();
        if (in_array('old_alias', $tables)){
            $old_alias = $this->model->get($this->table, [
                'fields' => ['alias'],
                'where' => [$this->columns['id_row'] => $id]
            ])[0]['alias'];

            if ($old_alias && $old_alias !== $_POST['alias']){

                $this->model->delete('old_alias', [
                    'where' => ['alias' => $old_alias, 'table_name' => $this->table]
                ]);

                $this->model->delete('old_alias', [
                    'where' => ['alias' => $_POST['alias'], 'table_name' => $this->table]
                ]);

                $this->model->add('old_alias', [
                    'fields' => ['alias' => $old_alias, 'table_name' => $this->table, 'table_id' => $id]
                ]);

            }
        }
    }
}