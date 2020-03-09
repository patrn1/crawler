<?php
namespace App\Models;

class Request extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;

    public function domain()
    {
        return $this->hasOne('App\Models\Domain');
    }

    public function path()
    {
        return $this->hasOne('App\Models\Path');
    }

    public function element()
    {
        return $this->hasOne('App\Models\Element');
    }
}
