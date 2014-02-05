<?php

    class CategoryAdmin extends LeftAndMain{
        static $url_segment = 'categories';

        static $url_rule = '/$Action/$ID/$OtherID';

        static $menu_title = 'Category edit';

        static $menu_priority = 10;
        static $url_priority = 39;

        static $tree_class = "Category";

        static $allowed_actions = array(
            'getsubtree',
            'treeview',
            'EditForm'
        );

        public function index($request) {
            if(!$request->param('Action')) $this->setCurrentPageId(null);            
            return parent::index($request);
        }

        public function init(){
            parent::init();            
            Requirements::css(CMS_DIR . '/css/screen.css');
            Requirements::css('catalog/css/CategoryAdmin.css');
            Requirements::javascript("catalog/javascript/CategoryAdmin.js");
        }
        
        public function getResponseNegotiator() {            
            $negotiator = parent::getResponseNegotiator();            
            $controller = $this;
            return $negotiator;
	}

        public function getList(){
            $category = $this->currentPage();
            return Category::get()->where("ParentID = $category->ID");
        }

        public function getEditForm($id = NULL, $fields = NULL){
            $form = parent::getEditForm($id, $fields);            
            
            $category = ($id && is_numeric($id)) ? Category::get()->byID($id) : $this->currentPage();
            $title = ($category && $category->exists()) ? $category->Title : _t('AssetAdmin.FILES', 'Files');
            
            if(!$fields) $fields = $form->Fields();
            $actions = $form->Actions();
            
            $fields = $form->Fields();

            $fields->push(new HiddenField('ID', false, $category ? $category->ID : null));
            
            //$actions = $category->getCMSActions();
            
            if($category->hasMethod('getCMSValidator')) {
                    $validator = $record->getCMSValidator();
            } else {
                    $validator = new RequiredFields();
            }

            $form = new Form($this, "EditForm", $fields, $actions, $validator);
            $form->loadDataFrom($category);
            $form->disableDefaultAction();
            $form->addExtraClass('cms-edit-form');
            $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));
            
            $form->addExtraClass('center ' . $this->BaseCSSClasses());
                        
            $form->setAttribute('data-pjax-fragment', 'CurrentForm');
            //$form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');

            $this->extend('updateEditForm', $form);

            return $form;
        }

        public function currentPageID() {
            if(is_numeric($this->request->requestVar('ID')))	{
                return $this->request->requestVar('ID');
            } elseif (is_numeric($this->urlParams['ID'])) {
                return $this->urlParams['ID'];
            } elseif(Session::get("{$this->class}.currentPage")) {
                return Session::get("{$this->class}.currentPage");
            } else {
                return 0;
            }
        }

        public function currentPage() {
            $id = $this->currentPageID();
            if($id && is_numeric($id) && $id > 0) {
                $category = Category::get()->byID($id); //DataObject::get_by_id('Folder', $id);
                if($category && $category->exists()) {
                    return $category;
                }
            }
            return new Category();
        }

        public function treeview($request) {             
            return $this->renderWith($this->getTemplatesWithSuffix('_TreeView'));
        }

        public function SiteTreeAsUL() {
            return $this->getSiteTreeFor($this->stat('tree_class'), null, 'ChildFolders');
        }

        public function currentCategoryID(){
            $category = Category::get()->First();
            return $category->ID;
        }

        public function LinkPageAdd($extraArguments = null) {
            $link = singleton("CategoryAddController")->Link();
            $this->extend('updateLinkPageAdd', $link);
            if($extraArguments) $link = Controller::join_links ($link, $extraArguments);
            return $link;
        }
        
        public function LinkCategoryEdit($id = null) {
            if(!$id) $id = $this->currentPageID();
            return $this->LinkWithSearch(
                    Controller::join_links(singleton('CategoryAdminEditController')->Link('show'), $id)
            );
	}
                
	public function LinkTreeView() {            
            return $this->LinkWithSearch(singleton('CategoryAdmin')->Link('treeview'));
	}
        
        public function LinkListView() {            
            return false;
            //return $this->LinkWithSearch(singleton('CategoryAdmin')->Link('treeview'));
	}
        
        protected function LinkWithSearch($link) {
		// Whitelist to avoid side effects
		$params = array(
			'q' => (array)$this->request->getVar('q'),
			'ParentID' => $this->request->getVar('ParentID')
		);
		$link = Controller::join_links(
			$link,
			array_filter(array_values($params)) ? '?' . http_build_query($params) : null
		);
		$this->extend('updateLinkWithSearch', $link);
		return $link;
	} 
        
        public function getSiteTreeFor($className, $rootID = null, $childrenMethod = null, $numChildrenMethod = null,
			$filterFunction = null, $minNodeCount = 30) {

		// Filter criteria
		$params = $this->request->getVar('q');
		if(isset($params['FilterClass']) && $filterClass = $params['FilterClass']){
			if(!is_subclass_of($filterClass, 'CMSSiteTreeFilter')) {
				throw new Exception(sprintf('Invalid filter class passed: %s', $filterClass));
			}
			$filter = new $filterClass($params);
		} else {
			$filter = null;
		}

		// Default childrenMethod and numChildrenMethod
		if(!$childrenMethod) $childrenMethod = ($filter && $filter->getChildrenMethod())
			? $filter->getChildrenMethod() 
			: 'AllChildrenIncludingDeleted';

		if(!$numChildrenMethod) $numChildrenMethod = 'numChildren';
		if(!$filterFunction) $filterFunction = ($filter) ? array($filter, 'isPageIncluded') : null;

		// Get the tree root
		$record = ($rootID) ? $this->getRecord($rootID) : null;
		$obj = $record ? $record : singleton($className);
		
		// Mark the nodes of the tree to return
		if ($filterFunction) $obj->setMarkingFilterFunction($filterFunction);

		$obj->markPartialTree($minNodeCount, $this, $childrenMethod, $numChildrenMethod);
		
		// Ensure current page is exposed
		if($p = $this->currentPage()) $obj->markToExpose($p);
		
		// NOTE: SiteTree/CMSMain coupling :-(
		if(class_exists('SiteTree')) {
			SiteTree::prepopulate_permission_cache('CanEditType', $obj->markedNodeIDs(),
				'SiteTree::can_edit_multiple');
		}

		// getChildrenAsUL is a flexible and complex way of traversing the tree
		$controller = $this;
		$recordController = singleton('CategoryAdminEditController');
                
		$titleFn = function(&$child) use(&$controller, &$recordController) {
			$link = Controller::join_links($recordController->Link("show"), $child->ID);
			return LeftAndMain_TreeNode::create($child, $link, $controller->isCurrentPage($child))->forTemplate();
		};

		$html = $obj->getChildrenAsUL(
			"",
			$titleFn,
			singleton('CMSPagesController'),
			true, 
			$childrenMethod,
			$numChildrenMethod,
			$minNodeCount
		);

		// Wrap the root if needs be.
		if(!$rootID) {
			$rootLink = $this->Link('show') . '/root';
			
			// This lets us override the tree title with an extension
			if($this->hasMethod('getCMSTreeTitle') && $customTreeTitle = $this->getCMSTreeTitle()) {
				$treeTitle = $customTreeTitle;
			} elseif(class_exists('SiteConfig')) {
				$siteConfig = SiteConfig::current_site_config();
				$treeTitle =  Convert::raw2xml($siteConfig->Title);
			} else {
				$treeTitle = '...';
			}
			
			$html = "<ul><li id=\"record-0\" data-id=\"0\" class=\"Root nodelete\"><strong>$treeTitle</strong>"
				. $html . "</li></ul>";
		}

		return $html;
	}

    }