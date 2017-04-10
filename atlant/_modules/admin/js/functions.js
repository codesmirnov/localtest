
function explode( delimiter, string ) {

    var emptyArray = { 0: '' };

    if ( arguments.length != 2
        || typeof arguments[0] == 'undefined'
        || typeof arguments[1] == 'undefined' )
    {
        return null;
    }

    if ( delimiter === ''
        || delimiter === false
        || delimiter === null )
    {
        return false;
    }

    if ( typeof delimiter == 'function'
        || typeof delimiter == 'object'
        || typeof string == 'function'
        || typeof string == 'object' )
    {
        return emptyArray;
    }

    if ( delimiter === true ) {
        delimiter = '1';
    }

    return string.toString().split ( delimiter.toString() );
}

function load_ajax(url, target, callback) {
 	var height = $(target).height();
	var margin = Math.ceil(height/2);

	target.html('<target style="margin: '+margin+'px 0px; text-align: center;"><img src="/img/loader1.gif"></target>');

	$.get( url, {},
		function(data){
			$(target).html(data);
			eval($('script', $(data)).html());

			if (callback) {
				callback(data);
			}
		});
}

function wpInit(target) {
	target.css("left", ($('body').width() - target.width()) / 2);
	if (! target.hasClass('init')) {

		$('.close', target).unbind('click').click(function(){
			wpHide(target);
			return false;
		});

		target.draggable();

		target.addClass('init');
	}
}

function wpShow(target){
	target.css('top', (document.documentElement.scrollTop + 20) + 'px');

	if ($('.wp:visible').size() == 0) {
		$.blockUI({ message: null, baseZ: 100, focusInput: true});
	}

	target.removeClass('closed');

	wpInit(target);

	target.show();
}

function wpHide(target){
	if (! target.hasClass('closed')) {
		if ($('.wp:visible').size() == 1)
			$.unblockUI();

		target.addClass('closed');

		target.hide();
	}
}

function wpLoad(url, target, callback) {
	wpShow(target);

	load_ajax(url, $('.target', target), function() {

		wpInit(target);

		if (callback) {
			callback();
		}
	})
}