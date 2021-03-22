<?php


namespace core\base\controller;


trait BaseMethods
{

    protected function clearStr($str){
        if (is_array($str)){
            foreach ($str as $key => $item) {
                $str[$key] = $this->clearStr($item);
            }
        } else {
            return trim(strip_tags($str));
        }
    }

    //функция возвращает число, даже если принимает строку
    protected function clearNum($num){
        return (!empty($num) && preg_match('/\d/', $num)) ?
            preg_replace('/[^\d.]/', '', $num) * 1 : 0;
    }

    protected function isPost(){
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    protected function isAjax(){
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    protected function redirect($http = false, $code = false){
        if ($code){
            $codes = ['301' => 'HTTP/1.1 301 Move Permanently'];
            if ($codes[$code]) header($codes[$code]);
        }
        if ($http) $redirect = $http;
        else $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : PATH;
        header("Location: $redirect");
        exit;

    }

    protected function writeLog($message, $file = 'log.txt', $event = 'Fault'){
        $dateTime = new \DateTime();
        $str = $event . ': ' . $dateTime->format('d-m-Y G:i:s') . ' - ' . $message . "\r\n";
        file_put_contents('log/' . $file, $str, FILE_APPEND);
    }

    protected function getStyles(){
        if ($this->styles){
            foreach ($this->styles as $style){
                echo '<link rel="stylesheet" href="' . $style . '">';
            }
        }
    }

    protected function getScripts(){
        if ($this->scripts){
            foreach ($this->scripts as $script){
                echo '<script src="' . $script . '"></script>';
            }
        }
    }

    protected function recursiveArr($arr, $deep = 0, $parentId = null, $rowId = 'id', $rowParentId = 'parent_id', $start = true ){
        $resArr = [];
        reset($arr);
        if ($deep){
            if (is_array($deep)){
                if ($deep['from'] < $deep['to']) $deep['from']++;
            }else{
                $deep = ['from' => 0, 'to' => $deep];
            }
        }
        while (($key = key($arr)) !== null){
            if ($arr[$key][$rowParentId] === $parentId){
                $resArr[$arr[$key][$rowId]] = $arr[$key];
                unset($arr[$key]);
                reset($arr);
                continue;
            }
            if (isset($resArr[$arr[$key][$rowParentId]])){
                $res = $this->recursiveArr($arr, $deep, $arr[$key][$rowParentId], $rowId, $rowParentId, false);
                if (!empty($res['resArr'])){
                    if ($deep && is_array($deep) && $deep['from'] === $deep['to']){
                        foreach ($res['resArr'] as $item){
                            $resArr[$item[$rowId]] = $item;
                        }
                    }else{
                        $resArr[$arr[$key][$rowParentId]]['sub'] = $res['resArr'];
                    }
                }
                if (isset($res['arr'])){
                    $arr = $res['arr'];
                    reset($arr);
                    continue;
                }
            }
            next($arr);
        }

        return $start ? $resArr : compact('resArr', 'arr');

    }
}