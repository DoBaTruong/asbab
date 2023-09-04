<?php

function id_parent ($id, $data, $column, $getcolumn, $idcompare = 0)
{
    while($id) {
        $d = $data->find($id);
        if($d->$column === $idcompare) {
            return $d->$getcolumn;
        }
        $id = $d->$column;
    }
}

function strcount($str, $delimiter)
{
    if(trim($str) !== '') {
        $arr = [];
        foreach (explode($delimiter, $str) as $v) {
            if(trim($v) !== '') {
                $arr[] = $v;
            }
        }

        return count($arr);
    }
}

function my_strpos($str, $delimiter, $s)
{
    if(trim($str) !== '') {
        $arr = [];
        foreach (explode($delimiter, $str) as $v) {
            if(trim($v) !== '') {
                $arr[] = $v;
            }
        }

        return in_array($s, $arr);
    }
}