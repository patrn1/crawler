<?php
namespace App\Models;

class Domain extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;

    /**
     * Get all of the paths for the domain.
     */
    public function paths()
    {
        return $this->belongsToMany('App\Models\Path', 'requests');
    }

    /**
     * Get all of the elements for the domain.
     */
    public function elements()
    {
        return $this->belongsToMany('App\Models\Element', 'requests');
    }
}
