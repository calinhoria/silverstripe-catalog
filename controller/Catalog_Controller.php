<?php

    class Catalog_Controller extends Page_Controller{

        private $_producturl;
        private $_categoryurl;
        private $_locale;

        public static $localesegment = array(
            "it" => "it_IT"
        );

        function init(){
            parent::init();
        }

        public function index(){

            $this->_producturl = $this->urlParams['URLSegmentProduct'];
            $this->_categoryurl = $this->urlParams['URLSegmentCategory'];
            $this->_locale = $this->urlParams['Locale'];

            if( $this->_producturl != NULL ){
                return $this->product();
            }elseif( $this->_categoryurl != NULL ){
                echo 2;
            }

        }

        public function product(){

            $t = ProductTranslation::get()->filter(
                array(
                    'URLSegment' => $this->_producturl,
                    'Locale' => self::$localesegment[$this->_locale]
                )
            )->first();

            if(!$t) return $this->httpError("404");

            $p = Product::get()->byID($t->ProductID);
            if(!$p->canView()) return $this->httpError("404");

            $data = array(
                "Title" => "ciao",
                "Product" => $p
            );

            return $this->customise($data)->renderWith(
                array("Product", "Page")
            );
        }
    }