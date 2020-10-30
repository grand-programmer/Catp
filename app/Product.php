<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'published',
        'deleted',
    ];
    public function categories(){
        return $this->belongsToMany('App\Category','category_product','product_id','category_id');
    }
}
