(function($) {
    $.entwine('ss', function($){
        $('.CategoryAdmin.cms-edit-form .ss-gridfield-item').entwine({
            onclick: function(e) {
                if($(e.target).closest('.action').length) {
                    this._super(e);
                    return;
                }
                var grid = this.closest('.ss-gridfield');
                if(this.data('class') == 'Category') {
                    var url = grid.data('urlFolderTemplate').replace('%s', this.data('id'));
                    $('.cms-container').loadPanel(url);
                    return false;
                }
                this._super(e);
            }
        });
    });
}(jQuery));
