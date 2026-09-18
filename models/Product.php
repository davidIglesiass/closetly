<?php

class Product
{
    public $id;
    public $category_id;
    public $seller_id;    // owning vendedor's user id, or null for a house/admin product
    public $name;
    public $description;
    public $price;
    public $discount;
    public $stock;
    public $image;
    public $created_at;
    public $categoryName; // set only by ProductRepository::findAllByCategory()
    public $sellerName;   // set only by ProductRepository::findAll()
    public $quantity;     // set only by OrderRepository::findProductsByOrder()
    public $images = [];  // set only by ProductRepository::attachImages() - cover image plus gallery extras
}
