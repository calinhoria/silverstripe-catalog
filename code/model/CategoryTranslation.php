<?php

    class CategoryTranslation extends DataObject{

        static $singular_name = 'Traduzione Categoria';

        public static $db = array(
            'Title' => 'Varchar(255)',
            'Content' => 'HTMLText',
            'Locale' => 'DBLocale',
            'URLSegment' => 'Varchar(255)',
            "MetaTitle" => "Varchar(255)",
            "MetaDescription" => "Text",
            "MetaKeywords" => "Varchar(255)",
            'SEOBody'=>'HTMLText',
            'SEOFooter'=>'HTMLText'
        );

        public static $has_one = array(
            'Category' => 'Category'
        );

        public static $summary_fields = array(
            'Title' => 'Nome',
            'valueLocale' => 'Lingua',
            'URLSegment' => 'URL'
        );
        
        function getCMSFields(){
            $fields = parent::getCMSFields();

            $fieldLang = new DropdownField(
                'Locale',
                'Lingua',
                $this->Locales(),
                '',
                null,
                'Seleziona una lingua'
            );

            $fields->addFieldToTab('Root.Main', $fieldLang);
            $fields->addFieldToTab("Root.Main", new TextField("Title", "Titolo"));
            
            if( $this->exists() ){
                $fields->addFieldToTab('Root.Main', new HtmlEditorField('Content','Content'));

                /*$baseLink = Controller::join_links (
                    Director::absoluteBaseURL()
                );
                $url = (strlen($baseLink) > 36) ? "..." .substr($baseLink, -32) : $baseLink;
                $urlsegment = new ProductURLSegmentField("URLSegment", $this->fieldLabel('URLSegment'));
                $urlsegment->setURLPrefix($url);

                $helpText = $this->fieldLabel('LinkChangeNote');

                if(!URLSegmentFilter::$default_allow_multibyte) {
                    $helpText .= $helpText ? '<br />' : '';
                    $helpText .= _t('SiteTreeURLSegmentField.HelpChars', ' Special characters are automatically converted or removed.');
                }
                $urlsegment->setHelpText($helpText);*/

                $fields->addFieldToTab('Root.Metadata', new TextField("MetaTitle", "Meta title"));
                //$fields->addFieldToTab('Root.Main', $urlsegment, 'Content');
                $fields->addFieldToTab('Root.Metadata', new TextareaField("MetaKeywords", "Meta keywords"));
                $fields->addFieldToTab('Root.Metadata', new TextareaField("MetaDescription", "Meta description"));

                $fields->addFieldsToTab("Root.Metadata", new TextareaField("SEOBody"));
                $fields->addFieldsToTab("Root.Metadata", new TextareaField("SEOFooter"));
            }else{
                $fields->removeByName("SEOFooter");
                $fields->removeByName("SEOBody");
                $fields->removeByName("MetaDescription");
                $fields->removeByName("MetaKeywords");

                $fields->removeByName("URLSegment");
                $fields->removeByName("MetaTitle");
                $fields->removeByName("Content");

            }
            //$fields->removeByName("CategoryID");
            
            return $fields;
        }
        
        function Locales() {
            $languages = Translatable::get_allowed_locales();
            $traslateLang = array();
            foreach($languages as $lang => $label){
                $traslateLang[$label] = i18n::get_locale_name($label);
            }
            return $traslateLang;
        }

        public function valueLocale() {
            if( $this->Locale ){
                $locales = Translatable::get_allowed_locales();
                return i18n::get_locale_name($this->Locale);
            }else{
                return "lingua non selezionata";
            }
        }
    }