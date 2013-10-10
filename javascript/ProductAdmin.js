(function($) {
    $.entwine('ss', function($) {
        $('#Hide input').entwine({
            onmatch:function(){
                if( this.is(":checked") )
                    $('.field.date').hide();
                else
                    $('.field.date').show();
            },
            onchange: function(){
                if( this.is(":checked") )
                    $('.field.date').hide();
                else
                    $('.field.date').show();
            },
            onmouseover: function(){
                if( this.is(":checked") )
                    $('.field.date').fadeTo( "slow", 0.33 );
                else
                    $('.field.date').fadeTo( "slow", 0.33 )
            },
            onmouseout: function(){
                if( this.is(":checked") )
                    $('.field.date').fadeTo( "slow", 0 );
                else
                    $('.field.date').fadeTo( "slow", 1 )
            }
        });
    });
})(jQuery);