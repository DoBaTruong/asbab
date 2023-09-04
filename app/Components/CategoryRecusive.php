<?php
namespace App\Components;

class CategoryRecusive {
    protected $data;
    protected $htmloption = '';
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function get_categories_recusive($selectedId = [], $id = 0, $text = '')
    {
        foreach ($this->data as $value) {
            if ($value->parent_id == $id) {
                in_array($value->id, $selectedId) ? $selected = 'selected' : $selected = '';
                $this->htmloption .=  '<option '.$selected.' value="'.$value->id.'">'.$text.$value->name.'</option>';
                $this->get_categories_recusive($selectedId, $value->id, $text.'--');
            }
        }

        return $this->htmloption;
    }
}