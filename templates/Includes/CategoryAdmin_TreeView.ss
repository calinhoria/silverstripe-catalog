<div class="cms-content-toolbar">
	<% include CategoryAdmin_ToolActions %>
</div>

<div class="ss-dialog cms-page-add-form-dialog cms-dialog-content" id="cms-page-add-form" title="<% _t('CMSMain.AddNew', 'Add new page') %>">
	$AddForm
</div>
<div class="center">
	<div class="cms-tree" data-url-tree="$Link(getsubtree)" data-url-savetreenode="$Link(savetreenode)" data-url-updatetreenodes="$Link(updatetreenodes)" data-url-addpage="{$LinkPageAdd('AddForm/?action_doAdd=1')}&amp;ParentID=%s&amp;PageType=%s&amp;SecurityID=$SecurityID" data-url-editpage="$LinkCategoryEdit('%s')" data-hints="$SiteTreeHints">	    
            $SiteTreeAsUL
	</div>
</div>
