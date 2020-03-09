<?php
namespace App\Models;

class Element extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;

    /**
     * The requests made for this element.
     */
    public function requests()
    {
        return $this->belongsTo('App\Models\Request', "id", 'element_id');
    }
}
