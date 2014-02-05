<?php

class GalleryImage extends Image {

    public static $has_one = array(
        'Product' => 'Product'
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeFieldFromTab("Root.Main","ProductID");
        $fields->removeFieldFromTab("Root.Main","SortOrder");
        return $fields;
    }

}