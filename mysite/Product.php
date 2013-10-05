<?php

    class Product extends DataObject{

        static $db = array(
            "Title" => "Varchar(255)",
            "URLSegment" => "Varchar(255)"
        );

        static $has_many = array(
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

        public function getCMSFields() {
            $fields = parent::getCMSFields();

            $fields->removeByName("ProductTranslations");

            $gfct = GridFieldConfig_RecordEditor::create();
            $gft = new GridField("ProductTranslations", "ProductTranslation", $this->ProductTranslations(), $gfct);
            $fields->addFieldToTab("Root.Traduzioni", $gft);

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