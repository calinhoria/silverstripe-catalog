<?php

class CategoryAdminEditController extends CategoryAdmin {

	static $url_segment = 'categories/edit';
	static $url_rule = '/$Action/$ID/$OtherID';
	static $url_priority = 41;	
	static $session_namespace = 'CategoryAdmin';
        
        
        public function init() {
            parent::init();
            
        }
}
