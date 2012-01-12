<?php

class Loading_Speed{

    static function getTitle(){
        return __("Loading Speed","myStat");
    }

    static function getMenuItemName(){
        return array(__('Loading Speed','myStat'),4);
    }

    static function getMenuTreeName(){
        return array(__('Audience','myStat'),1);
    }

    function init($date){
    }
};
?>