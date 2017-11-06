<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    private $dst;
    private $extension;
    public $name;

    function __construct($dst, $name, $extension=null) {
        if(strripos($dst, 'projects') > 0){
            $this->width = 640;
            $this->height = 360;
        }
        else if(strripos($dst, 'avatars') > 0){
            $this->width = 150;
            $this->height = 150;
        }

        $this->dictory= $dst;
        $this->name = $name;
        $this->dst = $dst.'/'.$name;

        if(is_null($extension)){
            $this->extension = config('constants.image.extension');
        }
    }

    public function getWidth(){
        return $this->width;
    }

    public function getHeight(){
        return $this->height;
    }

    public function getDestination($suffix = '', $extension=null){
        return $this->dst.$suffix.(is_null($extension) ? $this->extension : $extension);
    }

    public function setExtension($extension){
        return $this->extension = $extension;
    }
}
