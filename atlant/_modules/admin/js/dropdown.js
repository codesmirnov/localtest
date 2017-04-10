(function($) {

	var codeKeyUp = 38;
	var codeKeyDown = 40;
	var codeKeyLeft = 37;
	var codeKeyRight = 39;
	var codeKeyEnter = 13;
  var codeKeyTab = 9;

	function parseAjaxData(object, data) {
		var rows = jQuery.parseJSON(data);
    if (rows)
		for (i = 0; i < rows.length; i++) {
			$(object).append('<li class="ajax" id="' + rows[i].id + '">' + rows[i].title + '</li>');
		}
	}

	$.fn.suggestClick = function() {
		$(this).click(function() {
			var parent = $(this).parents('.dropdown');

			$(parent).addClass('selected');

			$('input[type=text]', parent).val($(this).text()).focus();
			$('input[type=hidden]', parent).val($(this).attr('id'));
			$('.get', parent).removeClass('acv');
			$('.list', parent).hide();

			parent.removeClass('acv');

			$('input', parent).trigger('change'); 
		});
	}

	$.fn.suggest = function (suggest_url, callback, onClickCallback) {
		var parent = this;

		function requestFormat(request){
			return request;
			request = request.replace(/[^а-яА-Я0-9\s\-]/g, '');
			if (request && request.length > 0) {
				return request;
			} else
				return false;
		}

		function find(request) {
			if (! request) {
				$('ul.ajax').hide();
				$('ul.default').show();
				$('li.acv', parent).removeClass('acv');
				$('ul.default li:first').addClass('acv');

				var count = $('ul li:visible', parent).size();
				if (count < 9) {
					$('.list', parent).removeClass('wrapper');
				} else {
					$('.list', parent).addClass('wrapper');
				}
				return false;
			}

			$.get(suggest_url, {q : request}, function(data) {
				$('.list', parent).show();
				$('ul.ajax').show().html('');
				$('ul.default').hide();

				if (callback) {
					callback($('ul.ajax', parent), data);
				} else
					parseAjaxData($('ul.ajax', parent), data);

				$('li.acv', parent).removeClass('acv');
				$('li.ajax:first', parent).addClass('acv');

				$('.ajax li', parent).suggestClick();

				var count = $('ul li:visible', parent).size();
				if (count < 9) {
					$('.list', parent).removeClass('wrapper');
				} else {
					$('.list', parent).addClass('wrapper');
				}
			});
		}

		parent.addClass('suggest');
    
    $('i, input', parent).click(function() {
		  //find(requestFormat($('input[type=text]', parent).val()));
		});

		$('input', parent).unbind('keydown').keydown(function(e) {
		  if (e.keyCode == 9)
        return true;
      $('input[type=hidden]', parent).val('');
			var acv = $('li.acv', parent);
			if (e.keyCode == codeKeyDown) {
				var next = acv.next(':visible');
				if (next.size()) {
					acv.removeClass('acv');
					next.addClass('acv');
				}
			} else
			if (e.keyCode == codeKeyUp) {
				var prev = acv.prev(':visible');
				if (prev.size()) {
					acv.removeClass('acv');
					prev.addClass('acv');
				}
			} else
			if (e.keyCode == codeKeyEnter) {
				$(acv).click();
				if (onClickCallback)
					onClickCallback();
				return false;
			}

			if (e.keyCode == 8) {
				var q = $(this).val();
				if (q)
					q = q.replace(/.$/g,'')

				find(requestFormat(q));
			}
		}).keypress(function(e) {
			if (e.keyCode != 9 && e.keyCode != 8 && e.keyCode != codeKeyEnter && e.keyCode != codeKeyUp && e.keyCode != codeKeyDown && e.keyCode != codeKeyLeft && e.keyCode != codeKeyRight) {
				find(requestFormat($(this).val() + String.fromCharCode(e.which)));
				$(parent).removeClass('selected');
			}
		}).bind('paste', function(e){
			var parent = this;
			setTimeout(function() {
				find(requestFormat($(parent).val()));
	    }, 50);
		})

	}

	$.fn.dropdown = function (suggest_url, callback, onClickCallback) {
		$(this).each(function() {
			var parent = this;

			$('li:first', parent).addClass('acv');
			$('.list', parent).hide();

			$('li', parent).hover(function() {
				$('.acv', parent).removeClass('acv');
				$(this).addClass('acv');
			});      

			$('i, input', parent).unbind('click').click(function() {
				var parent = $(this).parents('.dropdown');

				parent.toggleClass('acv').addClass('current');
				$('.list', parent).toggle();

				var others = $('.dropdown:not(.current)');
				others.removeClass('acv');
				$('.list', others).hide();

				parent.removeClass('current');

				var count = $('ul li:visible', parent).size();
				if (count < 9) {
					$('.list', parent).removeClass('wrapper');
				} else {
					$('.list', parent).addClass('wrapper');
				}

				return false;
			}).keydown(function(e) {
		  if (e.keyCode == 9)
        return true;
				var acv = $('li.acv', parent);
				if (e.keyCode == codeKeyDown) {
					var next = acv.next(':visible');
					if (next.size()) {
						acv.removeClass('acv');
						next.addClass('acv');
					}
				} else
				if (e.keyCode == codeKeyUp) {
					var prev = acv.prev(':visible');
					if (next.size()) {
						acv.removeClass('acv');
						prev.addClass('acv');
					}
				} else
				if (e.keyCode == codeKeyEnter) {
					$('li.acv', parent).click();
				}
			})

			$('li', parent).suggestClick();

			$(parent).suggest(suggest_url, callback, onClickCallback);

			$(document).click(function() {
				$('.list', parent).hide();
			})
		});

		return this;
	};
})(jQuery);
