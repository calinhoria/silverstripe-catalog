<?php

    class Product extends DataObject{

        static $db = array(
            "Title" => "Varchar(255)",
            "Hide" => "Boolean",
            "Start" => "Date",
            "End" => "Date"
        );

        static $has_one = array(
            "PrincipalImage" => "Image"
        );

        static $has_many = array(
            "Gallery" => "GalleryImage",
            "ProductTranslations" => "ProductTranslation"
        );

        static $searchable_fields = array(
            'Title' => array(
                'title' => 'Nome del Prodotto',
                'field' => 'TextField',
                'filter' => 'PartialMatchFilter',
            )
        );

        static $summary_fields = array(
            'Title' => 'Nome del prodotto'
        );

        public function canView($member = NULL){
            if(Permission::check('ADMIN')) return true;
            if( $this->Hide ) return false;
            $date = date("Y-m-d");
            if( $this->Start <= $date )
                if( $this->End == NULL )
                    return true;
                elseif( $this->End > $date )
                    return true;
                else
                    return false;
            elseif( $this->Start > $date )
                return false;
            if( $this->End <= $date )
                return false;
            return true;
        }

        public function getCMSFields() {
            $fields = parent::getCMSFields();

            $start = new DateField("Start", "Visibile Da");
            $start->setConfig('showcalendar', true);
            $end = new DateField("End", "Visibile Fino");
            $end->setConfig('showcalendar', true);

            $fields->addFieldToTab("Root.Visibility", new CheckboxField("Hide", "Nascondi"));
            $fields->addFieldToTab("Root.Visibility", $start);
            $fields->addFieldToTab("Root.Visibility", $end);

            $fields->addFieldToTab("Root.Images", new UploadField("PrincipalImage", _t("Product.PRINCIPALIMAGE", "PrincipalImage")));

            $gfct = GridFieldConfig_RecordEditor::create();
            $gft = new GridField("ProductTranslations", "ProductTranslation", $this->ProductTranslations(), $gfct);
            $fields->addFieldToTab("Root.Translations", $gft);

            $gallery = new SortableUploadField("Gallery");
            $gallery->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
            $fields->addFieldToTab('Root.Images', $gallery);

            $fields->fieldByName('Root.Translations')->setTitle(_t("ProductAdmin.TRANSLATIONS", "Translations"));
            $fields->fieldByName('Root.Visibility')->setTitle(_t("ProductAdmin.TABVISIBILITY", "Visibility"));
            $fields->fieldByName('Root.Images')->setTitle(_t("ProductAdmin.TABIMAGES", "Images"));

            $fields->removeByName("ProductTranslations");
            $fields->removeByName("Gallery");

            return $fields;
        }

        public function singular_name() {
            return _t($this->class.'.SINGULARNAME', "Prodotto");
        }

        public function plural_name() {
            return _t($this->class.'.PLURALNAME', "Prodotti");
        }

        function Locales() {
            $languages = Translatable::get_allowed_locales();
            $traslateLang = array();
            foreach($languages as $lang => $label){
                $traslateLang[$label] = i18n::get_locale_name($label);
            }
            return $traslateLang;
        }

    }