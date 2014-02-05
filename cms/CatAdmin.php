<?php

    class CatAdmin extends ModelAdmin{

        public static $managed_models = array(
            'Category'
        );

        static $url_segment = 'ccc';
        static $menu_title = 'ccc';

        public function init(){
            parent::init();
        }

    }