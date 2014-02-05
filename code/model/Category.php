<?php

    class Category extends DataObject{

        static $db = array(
            "Title" => "Varchar(255)",
            "Hide" => "Boolean"
        );
        
        static $has_many = array(
            "CategoryTranslations" => "CategoryTranslation"
        );
        
        static $many_many = array(
            "Products" => "Product"
        );

        static $extensions = array(
            "Hierarchy"
        );
        
        public function getLink(){
            return "";
        }
        
        public function getCMSFields() {            
            $fields = parent::getCMSFields();
            
            $fields->removeByName("ParentID");
            $fields->removeByName("Products");            
            $fields->removeByName("CategoryTranslations");
            
            $gfct = GridFieldConfig_RecordEditor::create();
            $gft = new GridField("CategoryTranslations", "CategoryTranslation", $this->CategoryTranslations(), $gfct);
            $fields->addFieldToTab("Root.Translations", $gft);
                                                   
            return $fields;
        }

        public function getChildren(){            
            return Category::get()->filter(array(
                "ParentID" => $this->ID,
                "Hide" => 0                
            ));            
        }

        public function getTreeTitle(){
            return $this->Title;
        }

        public function CMSTreeClasses() {
            $classes = sprintf('class-%s', $this->class);

            if($this->Hide)
                $classes .= " hide";

            $classes .= $this->markingClasses();

            return $classes;
        }

        public function ChildFolders() {
            return Category::get()->where("\"ParentID\" = " . (int)$this->ID);
        }

        public function hasChildFolders() {
            $SQL_folderClasses = Convert::raw2sql(ClassInfo::subclassesFor('Category'));
            return (bool)DB::query("SELECT COUNT(*) FROM \"Category\" WHERE \"ParentID\" = " . (int)$this->ID
            . " AND \"ClassName\" IN ('" . implode("','", $SQL_folderClasses) . "')")->value();
        }

    }