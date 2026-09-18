<?php

class Category
{
    public $id;
    public $name;
    public $parent_id;      // null for a top-level department (Men/Women/Children); set for a subcategory (T-Shirts/Hoodies/...)
    public $created_at;
    public $departmentName; // set only by CategoryRepository::findAll()

    public function isDepartment(): bool
    {
        return $this->parent_id === null;
    }
}
