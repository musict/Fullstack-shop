<?php


namespace core\admin\controller;


use core\base\settings\Settings;

class DeleteController extends BaseAdmin
{
    protected function inputData()
    {
        if (!$this->userId) $this->execBase();
        $this->createTableData();
        if (!empty($this->parameters[$this->table])){

            $id = is_numeric($this->parameters[$this->table]) ?
                $this->clearNum($this->parameters[$this->table]) :
                $this->clearStr($this->parameters[$this->table]);

            if ($id){

                $this->data = $this->model->get($this->table, [
                   'where' => [$this->columns['id_row'] => $id]
                ]);

                if ($this->data){
                    $this->data = $this->data[0];
                    if (count($this->parameters) > 1){
                        $this->checkDeleteFile();
                    }

                    $settings = $this->settings ?: Settings::instance();
                    $files = $settings::get('fileTemplates');

                    if ($files){
                        foreach ($files as $file) {
                            foreach ($settings::get('templateArr')[$file] as $item) {
                                if (!empty($this->data[$item])){

                                    $fileData = json_decode($this->data[$item], true) ?: $this->data[$item];

                                    if (is_array($fileData)){
                                        foreach ($fileData as $f) {
                                            @unlink($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . $f);
                                        }
                                    }else{
                                        @unlink($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . $fileData); //$f
                                    }
                                }
                            }
                        }

                    }

                    if (!empty($this->data['menu_position'])){

                        $where = [];

                        if (!empty($this->data['parent_id'])){

                            $pos = $this->model->get($this->table, [
                               'fields' => ['COUNT(*) as count'],
                               'where' => ['parent_id' => $this->data['parent_id']],
                               'no_concat' => true
                            ])[0]['count'];

                            $where = ['where' => 'parent_id'];

                        }else{

                            $pos = $this->model->get($this->table, [
                                'fields' => ['COUNT(*) as count'],
                                'no_concat' => true
                            ])[0]['count'];
                        }

                        $this->model->updateMenuPosition($this->table, 'menu_position', [$this->columns['id_row'] => $id], $pos, $where);
                    }

                    if ($this->model->delete($this->table, ['where' => [$this->columns['id_row'] => $id]])){
                        $tables = $this->model->showTables();

                        if (in_array('old_alias', $tables)){
                            $this->model->delete('old_alias', [
                                'where' => [
                                    'table_name' => $this->table,
                                    'table_id' => $id
                                ]
                            ]);
                        }

                        $manyToMany = $settings::get('manyToMany');
                        if ($manyToMany){
                            foreach ($manyToMany as $mTable => $tables) {
                                $targetKey = array_search($this->table, $tables);

                                if ($targetKey !== false){
                                    $this->model->delete($mTable, [
                                       'where' => [$tables[$targetKey] . '_' . $this->columns['id_row'] => $id]
                                    ]);
                                }
                            }
                        }

                        $_SESSION['res']['answer'] = '<div class="success">' . $this->messages['deleteSuccess'] . '</div>';

                        $this->redirect($this->adminPath . 'show/' . $this->table);
                    }
                }
            }

        }

        $_SESSION['res']['answer'] = '<div class="error">' . $this->messages['deleteFail'] . '</div>';
        $this->redirect();
    }

    protected function checkDeleteFile(){

        unset($this->parameters[$this->table]);

        $updateFlag = false;

        foreach ($this->parameters as $row => $item) {

            $item = base64_decode($item);

            if (!empty($this->data[$row])){

                $data = json_decode($this->data[$row], true);

                if ($data){
                    foreach ($data as $key => $value) {
                        if ($item === $value){

                            $updateFlag = true;

                            @unlink($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . $item);
                            unset($data[$key]);
                            $this->data[$row] = $data ? json_encode($data) : 'NULL';

                            break;
                        }
                    }
                }
                elseif($this->data[$row] === $item){

                    $updateFlag = true;

                    @unlink($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . $item);
                    $this->data[$row] = 'NULL';
                }
            }
        }

        if ($updateFlag){
            $this->model->edit($this->table, [
               'fields' => $this->data
            ]);

            $_SESSION['res']['answer'] = '<div class="success">' . $this->messages['editSuccess'] . '</div>';
        }
        else{
            $_SESSION['res']['answer'] = '<div class="error">' . $this->messages['editFail'] . '</div>';

        }
        $this->redirect();
    }
}