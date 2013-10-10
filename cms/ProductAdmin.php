<?php

    class ProductAdmin extends ModelAdmin{

        public static $managed_models = array(
            'Product'
        );

        static $url_segment = 'product';
        static $menu_title = 'Prodotti';

        public function init(){
            parent::init();
        }

    }