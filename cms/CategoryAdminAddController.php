<?php

    class CategoryAddController extends CategoryAdmin{

        static $url_segment = 'categories/add';
        static $url_rule = '/$Action/$ID/$OtherID';
        static $url_priority = 42;
        static $menu_title = 'Add Category';
        static $session_namespace = 'CategoryAdmin';

        static $allowed_actions = array(
            'AddForm',
            'doAdd',
        );

        public function AddForm() {
            $record = $this->currentPage();

            $numericLabelTmpl = '<span class="step-label"><span class="flyout">%d</span><span class="arrow"></span><span class="title">%s</span></span>';

            $topTitle = _t('CMSPageAddController.ParentMode_top', 'Top level');
            $childTitle = _t('CMSPageAddController.ParentMode_child', 'Under another page');

            $fields = new FieldList(
                new LiteralField('PageModeHeader', sprintf($numericLabelTmpl, 1, _t('CMSMain.ChoosePageParentMode', 'Choose where to create this page'))),

                $parentModeField = new SelectionGroup(
                    "ParentModeField",
                    array(
                        "top//$topTitle" => null, //new LiteralField("Dummy", ''),
                        "child//$childTitle" => $parentField = new TreeDropdownField(
                            "ParentID",
                            "",
                            'Category',
                            'ID',
                            'TreeTitle'
                        )
                    )
                )
            );
            // TODO Re-enable search once it allows for HTML title display,
            // see http://open.silverstripe.org/ticket/7455
            // $parentField->setShowSearch(true);
            $parentModeField->setValue($this->request->getVar('ParentID') ? 'child' : 'top');
            $parentModeField->addExtraClass('parent-mode');

            // CMSMain->currentPageID() automatically sets the homepage,
            // which we need to counteract in the default selection (which should default to root, ID=0)
            $homepageSegment = RootURLController::get_homepage_link();
            if($record && $record->URLSegment != $homepageSegment) {
                $parentField->setValue($record->ID);
            }

            $actions = new FieldList(
            // $resetAction = new ResetFormAction('doCancel', _t('CMSMain.Cancel', 'Cancel')),
                FormAction::create("doAdd", _t('CMSMain.Create',"Create"))
                    ->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept')
                    ->setUseButtonTag(true)
            );

            $this->extend('updatePageOptions', $fields);

            $form = new Form($this, "AddForm", $fields, $actions);
            $form->addExtraClass('cms-add-form stacked cms-content center cms-edit-form ' . $this->BaseCSSClasses());
            $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

            if($parentID = $this->request->getVar('ParentID')) {
                $form->Fields()->dataFieldByName('ParentID')->setValue((int)$parentID);
            }

            return $form;
        }

        public function doAdd($data, $form){
            $parentID = isset($data['ParentID']) ? (int)$data['ParentID'] : 0;

            $record = new Category();
            $record->ParentID = $parentID;
            try {
                $record->write();
            } catch(ValidationException $ex) {
                $form->sessionMessage($ex->getResult()->message(), 'bad');
                return $this->getResponseNegotiator()->respond($this->request);
            }

            return $this->redirect(Controller::join_links(singleton('CategoryAdmin')->Link('show'), $record->ID));
        }
    }