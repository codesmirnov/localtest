(function(jQuery){
	
	jQuery.hotkeys = {
		version: "0.8",

		specialKeys: {
			8: "backspace", 9: "tab", 13: "return", 16: "shift", 17: "ctrl", 18: "alt", 19: "pause",
			20: "capslock", 27: "esc", 32: "space", 33: "pageup", 34: "pagedown", 35: "end", 36: "home",
			37: "left", 38: "up", 39: "right", 40: "down", 45: "insert", 46: "del", 
			96: "0", 97: "1", 98: "2", 99: "3", 100: "4", 101: "5", 102: "6", 103: "7",
			104: "8", 105: "9", 106: "*", 107: "+", 109: "-", 110: ".", 111 : "/", 
			112: "f1", 113: "f2", 114: "f3", 115: "f4", 116: "f5", 117: "f6", 118: "f7", 119: "f8", 
			120: "f9", 121: "f10", 122: "f11", 123: "f12", 144: "numlock", 145: "scroll", 191: "/", 224: "meta"
		},
	
		shiftNums: {
			"`": "~", "1": "!", "2": "@", "3": "#", "4": "$", "5": "%", "6": "^", "7": "&", 
			"8": "*", "9": "(", "0": ")", "-": "_", "=": "+", ";": ": ", "'": "\"", ",": "<", 
			".": ">",  "/": "?",  "\\": "|"
		}
	};

	function keyHandler( handleObj ) {
		if ( typeof handleObj.data !== "string" ) {
			return;
		}
		
		var origHandler = handleObj.handler,
			keys = handleObj.data.toLowerCase().split(" ");
	
		handleObj.handler = function( event ) {
			if ( this !== event.target && (/textarea|select/i.test( event.target.nodeName ) ||
				 event.target.type === "text") ) {
				return;
			}
			
			var special = event.type !== "keypress" && jQuery.hotkeys.specialKeys[ event.which ],
				character = String.fromCharCode( event.which ).toLowerCase(),
				key, modif = "", possible = {};

			if ( event.altKey && special !== "alt" ) {
				modif += "alt+";
			}

			if ( event.ctrlKey && special !== "ctrl" ) {
				modif += "ctrl+";
			}
			
			if ( event.metaKey && !event.ctrlKey && special !== "meta" ) {
				modif += "meta+";
			}

			if ( event.shiftKey && special !== "shift" ) {
				modif += "shift+";
			}

			if ( special ) {
				possible[ modif + special ] = true;

			} else {
				possible[ modif + character ] = true;
				possible[ modif + jQuery.hotkeys.shiftNums[ character ] ] = true;

				if ( modif === "shift+" ) {
					possible[ jQuery.hotkeys.shiftNums[ character ] ] = true;
				}
			}

			for ( var i = 0, l = keys.length; i < l; i++ ) {
				if ( possible[ keys[i] ] ) {
					return origHandler.apply( this, arguments );
				}
			}
		};
	}

	jQuery.each([ "keydown", "keyup", "keypress" ], function() {
		jQuery.event.special[ this ] = { add: keyHandler };
	});

})( jQuery );

(function($) {
  $.priceFormat = function(price) {
  	fprice = '';
  	price = ' ' + price.toString();
  	if (price.length > 4)
  		for (i = 0; i < price.length; i++) {
  			if ((price.length - i) % 3 == 0 && i > 0) fprice += ' ';
  			fprice += price.charAt(i);
  		}
  	else fprice = price;
  	fprice = fprice.replace(/^([\s]+)/, '');
  	return fprice;    
  }
  
  
  $.fn.validate = function() {
    $(this).each(function() {
    	
      var form = this;
      var selectors = $('.required, .email', this);
      
      function required(input) {
        if (! $(input).hasClass('required') || $(this).hasClass('error'))
          return false;
        if ($(input).attr('type') == 'checkbox' && ! $(input).is(':checked'))
          return $(input).addClass('error');
          
        if ($(input).is('div')) {
          if ($('input[type=hidden]', input).size()) {
            input = $('input[type=hidden]', input);
          } else
            input = $('input', input);
        }
        
        if ($(input).val() == '') {
          $(input).addClass('error');
        }      
        
      }
      
      function email(input) {
        if (! $(input).hasClass('email') || $(this).hasClass('error'))
          return false;
        var reg = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (! reg.test($(input).val())) {
          $(input).addClass('error');
        }      
      }
      
      function minlength(input) {
        if (! $(input).attr('minlength') || $(this).hasClass('error'))
          return false;
        if ($(input).attr('minlength') && $(input).val().length < $(input).attr('minlength')) {
          $(input).addClass('error');
        }      
      }
      
      function maxlength(input) {
        if (! $(input).attr('maxlength') || $(this).hasClass('error'))
          return false;
        if ($(input).attr('maxlength') && $(input).val().length > $(input).attr('maxlength')) {
          $(input).addClass('error');
        }     
      }
      
      function checkForm() {    
        if ($('.error', form).size() > 0) {
          $('input[type=submit]', form).attr('disabled', true);
          $('.input:has(.error)').addClass('error');
        } else {
          $('input[type=submit]', form).removeAttr('disabled');  
        }
      }
      
      function checkField(input) {
        $('*', input).removeClass('error'); 
        $(input).removeClass('error');  
        $(input).parent().removeClass('error'); 
        required(input);
        minlength(input);
        maxlength(input); 
        email(input);
      }
      
      /*
      $(selectors).keyup(function() {
        checkField(this);
        checkForm();
      }).change(function() {
        checkField(this);
        checkForm();
      }).each(function() {
        checkField(this);
        $('input.error:first').focus();
      })  */    
      
      checkForm();
      
      function lister() {
      	$(selectors).each(function() {
	        checkField(this);
	      });        
      	checkForm();
				setTimeout(lister, 50);
      }
      
      lister();      
      //$('div.error', form).removeClass('error');
    })
  }
})(jQuery);

(function($) {
	$.fn.atlantTreeControll = function(params) {
    var parent;
    
    var o_just_added_parent;  //содержит текущего родителя только что добавленного раздела
    var f_just_added = false; //флаг разрешает повторное добавления раздела к текущему родителю
    var f_added = false;      //флаг опредляет добавления раздела
    
    var h_added = false;
    var h_just_added = true; //флаг разрешающий вывод подсказки о повторном добавления более раза
      
    var dblclick = false;
    
    params.baseURL = $('#site-map').attr('href');
    
    //сохранение имени раздела
    var saveName = function(object, add_parent) {
      $('input.tree-controll', object).each(function() {
        var parent = $(this).parent();
        var title = $(this).val();
        var object = $('a:first', parent);
        if (! $(this).val()) {
          $(parent).remove();  
        } else {        
          $(object).show().text($(this).val()).focus();
          $(this).remove();          
        }
        
        $('.layout-help').fadeOut();
        
        f_added = false;
        f_just_added = true;   
        dblclick = false;   
        
        if (add_parent) {
        	var model = $(parent).attr('model');
          var parent_link = $('a:first', add_parent);
          var parent_id = $(parent_link).attr('rel');
          var href = $(parent_link).attr('href');
          $.post(href + '/add', {
            'parent_id' : parent_id,
            'title'     : title
          }, function(data) {
            var data = jQuery.parseJSON(data);
            if (! data.id)
              alert('Ошибка. Потерян ID раздела.');
            $(object).attr('rel', data.id);
            $(object).attr('href', params.baseURL + data.id);
          })
        }
      });
    }
    
    //редактирования имени раздела
    var editName = function(object, add_parent) {
      var parent = $(object).parent();
      $(object).hide().after('<input class="tree-controll" type="text" value="' + $(object).text().replace('"', '') + '"/>');
      $('input.tree-controll', parent).focus().keypress(function(e) {
        if (e.which == 13) {
          saveName(parent, add_parent);
          if (h_just_added) {
            $('.layout-help').stop().html('<p>Если хотите добавить еще один раздел, повторно нажмите <b>Enter</b></p>');
            $('.layout-help').fadeIn(function() {
              $(this).delay(2000).fadeOut();
            });
            h_just_added = false;
          }
          
          return false;
        }
      });
      
      $('.layout-help').html('<p>Для сохранения нажмите <b>Enter</b></p>');
      $('.layout-help').fadeIn();            
    }
    
    //добавления нового раздела к object
    var add = function(object) {
      f_added = true;    
      o_just_added_parent = object; 
      
      object = object.parent();
      if (! $('ul', object).size()) 
        $(object).append('<ul>');
        
      //var randName = 'Новый раздел ' + parseInt($('li', object).size() + 1);
      $('ul:first', object).show().append('<li><div class="handle"><a href="#"></a></div></li>');    
      basicControll($(object));  
      moveControll($('li', object));
      
      //saveName();
      editName($('li:last a', object), object);  
    }
    
    //добавляет элементы управления к списку:
    // .toggle - показ/скрытие внутренних веток дерева
    // .add - добавления новой ветки
    var basicControll = function(object) {
      var li = $(object).parent();
        
      if ($('ul:first', li).size())
        $(object).prepend('<span class="toggle">-</span>');
        $('a', object).after('<span class="add">+</span>');   
      
      $('.toggle', object).click(function() {
        var parent = $(this).parent(); 
        $('ul:first', li).toggle();
        if ($('ul:first', li).is(':visible'))
          $(this).text('-');
        else
          $(this).text('+');            
      }); 
      
      $('a', object).dblclick(function() {
        dblclick = true;
        saveName();
        editName($(this));        
      })
      
      if (params.callback) {
        $('a', object).unbind('click').click(function() {
          var object = this;
          setTimeout(function() {
            if (! dblclick)
              params.callback(object);       
          }, 400);
          return false;
        })
      }
      
      $('.add', object).unbind('click').click(function() {
        add($(this).parent());
      });      
      
      return object;   
    };
    
    var getParentId = function(object) {
      return $('a:first', $(object).parents('ul:first').parents('li:first'));
    }    
    
    var reorder = function(parent) {
      var hash = new Array();
      $('li', parent).each(function(i) {
        hash[i] = $('a:first', this).attr('rel');
      })
      $.post(params.baseURL + 'order', {'pos' : hash});
    }
    
    var remove = function(id, new_parent_id) {
      $.post(params.baseURL + 'remove', {'id' : id, 'parentId' : new_parent_id});
    }
    
    //драг-дроп управления деревом для object
    var ui_dropover = null;
    var ui_drag_parent;
    var moveControll = function(object) {
      $(object).draggable('destroy').droppable('destroy');
      $('ul li', object).draggable({
        delay : 100,
        revertDuration : 0,
        revert: true,
        handle: '.handle',
        cancel: 'a, span',
        start: function(event, ui) {
          ui_drag_parent = getParentId($(this));  
          $('ul', this).hide();
        },
        drag: function(event, ui) {
          if (ui_dropover)
            if ($(this).position().left - $(ui_dropover).position().left > 60)
              $('.drop-helper', parent).addClass('drop-helper-left');
            else
              $('.drop-helper', parent).removeClass('drop-helper-left');
        },
        stop: function() {
        }
      });
      
      $('.handle', object).droppable({
        over: function(event, ui) {  
          ui_dropover = $(this);
          $('.drop-helper', parent).remove();
          $(ui_dropover).append('<div class="drop-helper" />');          
        },
        deactivate: function(event, ui) {
          $('.drop-helper:first', parent).each(function() {
              
            if ($(this).is('.drop-helper-left')) {
              var parent = $(this).parents('li:first');
              if (! $('ul', parent).size()) 
                $(parent).append('<ul>');
              $('ul:first', parent).show().prepend(ui.draggable);    
              
              var current_parent = getParentId(ui.draggable);
              var start_parent = $(ui_drag_parent); 
              var start_parent_id = $(start_parent).attr('rel');
              var current_parent_id = $(current_parent).attr('rel');
              var id = $('a', ui.draggable).attr('rel');
              
                                                        
              if (start_parent_id != current_parent_id) {
                remove(id, current_parent_id);
              } else {
                reorder($(current_parent).parents('li:first'));
              }    
            } else {
              $('.drop-helper', parent).parents('li:first').after(ui.draggable);      
              
              var current_parent = getParentId(ui.draggable);
              var start_parent = $(ui_drag_parent); 
              var start_parent_id = $(start_parent).attr('rel');
              var current_parent_id = $(current_parent).attr('rel');
              var id = $('a', ui.draggable).attr('rel');
                                                        
              if (start_parent_id != current_parent_id) {
                remove(id, current_parent_id);
              }              
              reorder($(current_parent).parents('li:first'));   
            }
          }); 
          $('.drop-helper', parent).remove();
          $('.ui-draggable-dragging', parent).removeClass('ui-draggable-dragging').attr('style', '');
          $('ul', ui.draggable).show(); 
        }
      });      
    };
    
    $(this).each(function() {
      parent = this;
      
      $('.toggle, .add', parent).remove();
      
      $('li', this).each(function() {
        $('a:first', this).wrap('<div class="handle">');
        basicControll($('.handle:first', this));
      });
      
      $('li li li li ul', this).each(function() {
       $(this).hide();
       $('.toggle:first', $(this).parent()).text('+'); 
      });
      
      var is_draggin = false;
      var draggin_item;
      
      moveControll($(parent));
    });
    
    //$('a:first', this).focus();
    $(this).keypress(function (e) {
      if (e.which == 13) {
        if (f_just_added) {
          add(o_just_added_parent);
          f_just_added = false;
        }
        return false;
      }
    })
  };
  
  
  $.atlantSiteMap = function() {
  	$('#site-map').sideForm(function() {
  		$('#site-map-tree').atlantTreeControll({
	      baseURL  : '/admin/sitemap/',    
	      callback : function(object) {
	        $.post($(object).attr('href'), {}, function(data) {    
	            $('#site-map-branch-edit .target').html(data);
	            $('#site-map').sideFormShow();
	            $('#site-map-branch-edit form').atlantInitForm().ajaxForm({
	            	beforeSubmit: function() {
	      					$('#site-map-branch-edit').css('opacity', '0.2');            		
	            	},
					      success: function(data) {
	      					$('#site-map-branch-edit').css('opacity', '1');
			      			//formHide();
					      }
					    }).each(function() {
					    	var form = this;
						  	$('.delete-link', this).unbind('click').click(function() {
						  		var target = this;
									jConfirm('Подтвердите удаление', 'Удаление', function(r) {
										if (r) {					
								  		$.get($(target).attr('href'), {}, function() {
								  			$(form).remove();
								  			$(object).parent().remove();
			            			$('#sitemap').sideFormHide();
								  		})
											return false;
										}
									});
						  		return false;
						  	}); 				    	
					    })
	        })
	      }
	    });
  	})
    
    return this;
  }
})(jQuery);

(function($) {
  $.fn.sideFormShow = function() {
  	var parent    = this;
  	var sideForm  = $('.sideform', parent);
  	var workspace = $('.workspace', parent); 
  	
    $(parent).height('auto');
    $(workspace).width('auto');
    $(sideForm).attr('style', '').show().width($(parent).width());
    var jump = $(workspace).width() + parseInt($(workspace).css('padding-right'));
    $('.spacer', parent).animate({left: -jump}).attr('jump', -jump);
    var top = $(window).scrollTop() - $(parent).offset().top;
    if (top < 0) 
      top = 0;
    $(sideForm).css('margin-top', top + 'px');
    
    var is_closed = false;
    $(window).unbind('scroll').scroll(function() { 
      if ($(window).scrollTop() < top && ! is_closed) {
     	  $(parent).sideFormHide();
        is_closed = true;
      }
    }); 
		
		return this;     
  }      
  
  $.fn.sideFormHide = function(callback) {    
  	var parent    = this;
  	var sideForm  = $('.sideform', parent);
  	var workspace = $('.workspace', parent); 
			
    $('.spacer', parent).animate({left: 0});
    $(workspace).animate({width: $(parent).width()}, 500, callback);
		
		return this;
  }
  
  $.fn.sideForm = function(callback) {
  	var parent    = this;
  	var sideForm  = $('.sideform', parent);
  	var workspace = $('.workspace', parent); 
    
    $('.workspace', parent).width($(parent).width());
    
    $('.spacer', parent).draggable({
      axis: 'x',
      handle: '.handler',
      start: function() {
        //$(this).width($('#site-map').width());
      },
      stop: function() {
        var jump = $(this).attr('jump');
        var left = jump - parseInt($(this).css('left'));
        if (left < -150) {
        	$(parent).sideFormHide();
        } else {
          $(this).stop().animate({left: jump}, 100);          
        }  
        
        //$(this).css('width', 'auto');
      }
    });
    
    $('.back', sideForm).click(function() {
      $('.spacer', parent).animate({left: 0}, 'normal', function() {
      });
      $(workspace).animate({width: $(parent).width()});
      return false;      
    });    
    
    $(workspace).each(function() {
      $('.java', this).click(function() {
        return false;
      });
    });
    
    if (callback)
    	callback();
    
    return this;
  }
})(jQuery);

(function($) {
  $.fn.atlantInitForm = function() {    
  	var form = this;
  	$('.delete-link', this).click(function() {  
  		var target = this;
			jConfirm('Подтвердите удаление', 'Удаление', function(r) {
				if (r) {					
		  		$.get($(target).attr('href'), {}, function() {
		  			$(form).remove();
		  		})
					return false;
				}
			});
  		return false;
  	});
  	
    $('.elrte', this).each(function() {
  		elRTE.prototype.options.toolbars.editTable = ['copypaste', 'format', 'style', 'lists', 'links', 'elfinder', 'images', 'tables'];
  		var opts = {
  			toolbar  : 'editTable',
  			cssClass : 'el-rte',
  			lang     : 'ru',
  			height   : 396,
  			cssfiles : ['/_modules/admin/css/editor.css'],
  			fmOpen : function(callback) {
  					$('<div id="myelfinder" />').elfinder({
  						url : '/_modules/admin/js/elfinder-1.1/connectors/php/connector.php',
  						lang : 'ru',
  						dialog : { width : 900, modal : true, title : 'elFinder - file manager for web' },
  						closeOnEditorCallback : true,
  						editorCallback : callback
  					})
  				}
  		}
  		$(this).elrte(opts);
  	});
    
    $('.elrte-extrac-min', this).each(function() {
  		elRTE.prototype.options.toolbars.editTable = ['copypaste', 'links'];
  		var opts = {
  			toolbar  : 'editTable',
  			cssClass : 'el-rte',
  			lang     : 'ru',
  			//height   : 450,
  			cssfiles : ['/_modules/admin/css/editor.css']
  		}
  		$(this).elrte(opts);
  	});
  	
		$('.elrte-table-edit').each(function () {
			elRTE.prototype.options.toolbars.tableEdit = ['tables'];
			var opts = {
				cssClass : 'el-rte',
				lang     : 'ru',
				toolbar  : 'tableEdit',
				cssfiles : ['/_modules/admin/css/editor.css']
			}
			$(this).elrte(opts);
		});
		
		$('.dropdown-default').dropdown();
	  $('.atlant-field-types').dropdown(false, false, function(data) {
	  	var id = $('#i-type-id').val();
	  	$('#special-forms .special-form').hide();
	  	$('#special-form-' + id).show();
	  });
	  $('.categories .java').click(function() {
	    $('.categories .acv').removeClass('acv');
	    $(this).parent().addClass('acv');
	  })
	  
	  $('.categories .java').click(function() {
	    $('.categories .acv').removeClass('acv');
	    $(this).parent().addClass('acv');
	    $('#category-id').val($(this).attr('rel'));
	  })
	  swfMulti($('.swf-uploader'));
	  $('.ajax-explorer').ajaxExplorer();
  
  	$(form).validate();
		
		return this;
  }
})(jQuery);

(function( $ ) {    
  $.fn.ajaxExplorer = function() { 
    $(this).each(function() {
  		var parent = this;
      
      
    $('*', this).unbind();
  		
  		function serialize() {
  			var list = '';
  			$('.explorer-wrap .acv', parent).each(function() {
  				list += $(this).attr('itemid') + ',';
  			})
            
  			return list;
  		}
  		
  		function content(data) {			
  			$('.item', data).click(function() {
  				if (! $(this).hasClass('acv')) {					
  					$(this).addClass('acv');
  					$('.explorer-wrap .item[itemid="'+$(this).attr('itemid')+'"]', parent).remove();
  					$('.explorer-wrap', parent).append($(this).clone(true));
  				} else {				
  					$(this).removeClass('acv');
  					$('.explorer-wrap .item[itemid="'+$(this).attr('itemid')+'"]', parent).remove();
  				}
  				
  				$('input[type=hidden]', parent).val(serialize());
  				
  				return false;
  			})
  		}
  					
      content($('.explorer-wrap', parent));	
  		
  		$('.explore', this).click(function() {
  			if ($(this).hasClass('close')) {
  				$('.explorer', parent).hide();
  				$('.explorer-wrap', parent).show();
  				$(this).html('&darr; Выбрать').removeClass('close');
  			} else {
  				$('.explorer', parent).show();
  				$('.explorer-wrap', parent).hide();	
  				
  				$(this).html('&uarr; Закрыть').addClass('close');
  				$.get($(this).attr('href'), {}, function(data) {
  					$('.explorer', parent).html(data);	
  					content($('.explorer', parent));	
  				});				
  			}
  			
  			return false;
  		})
    })
 	}
 	
})( jQuery );


function tableInputs(parent, settings) {
	function calc(parent) {
		$('tr', parent).each(function(row) {
		  var i = row - 1;
			$('input:not(input[type=radio]), select, textarea', this).each(function(cel) {
				var name = $(this).attr('name').replace(/\[[\d]+\]/, '['+i+']');
				$(this).attr('name', name);
			});
      
      $('.pos', this).val(i);
		});
	}
  
  function dnd(parent) {
    $(parent).tableDnD({
    	onDrop: function(table, row) {
    		calc(parent);
    	}
    });
  }

	parent.change(function() {
		parent = this;
    
    if (! $('.controll', parent).size()) {
      $('tr:first', parent).append('<th class="controll"><span class="java add">+</span></th>');
      $('tr:not(tr:first)', parent).each(function() {
        $(this).append('<td class="controll"><span class="java del">&times;</span></td>');
      })
    }
    
		calc(parent);

		$('.add', parent).unbind('click').click(function() {
			parent = $(this).parents('table');
      clone  = $('tr:last', parent).clone();
      $(parent).append();
      $(parent).append(clone);
      $('input, textarea, select', clone).val('');
      $('input[type=checkbox]', clone).removeAttr('checked');
      
      $(parent).change();
		});
    
		$('.del', parent).unbind('click').click(function() {
			var row = $(this).parents('tr');
			if (! row.hasClass('empty')) {
				$(this).parents('tr').remove();
				calc(parent);
			}
		})
    
    dnd(parent);
  }).each(function() {
    $(this).change();
  })
}

function swfMulti(parent, callback) {
  var parent;
  var href;
  
  function serialize(parent) {
    var list = '';       
    $('.wrap img', parent).each(function(i) {
      list += (i > 0 ? ',' : '') + $(this).attr('itemid');
    });
    
    $('input[type=hidden]', parent).val(list);
    
    return list;
  }
  
  function order(parent) {
    $('.wrap', parent).sortable('destroy').sortable({
			out: function(event, ui) {
				$(ui.helper).addClass('del');
			},
			over: function(event, ui) {
				$(ui.helper).removeClass('del');
			},
			stop: function(event, ui) {
				if ($(ui.item).hasClass('del')) {
		  		var target = this;
					jConfirm('Подтвердите удаление', 'Удаление', function(r) {
						if (r) {					
							var id = $(ui.item).attr('itemid');
							$(ui.item).remove();
							$.post(href + 'del', {'id' : id});
							return false; 
						}
					});
				} else {
					$.post(href + 'order', {'list' : serialize($(parent))});
				}
			}
		});
  }
  
	var href;	
  $(parent).each(function() { 
  	parent = this;
  	href = $('.target', parent).attr('href');
    
  	var rand = Math.floor((Math.random()*1000000)+1);
    var id     = $(parent).attr('id') + '-' + rand;
    
    $(parent).attr('id', id);
  	$('.holder',   parent).attr('id', id + '-holder');
  	$('.progress', parent).attr('id', id + '-progress');
  	$('.cancel',   parent).attr('id', id + '-cancel');  
    
    if ($('.wrap img', parent).size())
      $('.wrap', parent).show();
    	
		$(parent).swfupload('destroy').swfupload({
			flash_url              : $(parent).attr('swf'),
			upload_url             : href + 'add?phpsessid=' + $(parent).attr('phpsessid') + '&token=' + rand,
			file_size_limit        : "3 MB",
			file_types             : $(parent).attr('extension'),
			file_types_description : "Изображения",
			custom_settings : {
				progressTarget : id + '-progress-' + rand,
				cancelButtonId : id + '-cancel-' + rand
			},
	
			button_placeholder_id : id + '-holder',
			button_width          : $('.target', parent).width(),
			button_height         : 30,
			button_window_mode    : SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor         : SWFUpload.CURSOR.HAND,
			debug                 : false
		}).
		bind('fileQueued', function(event, file) {
	    $(this).swfupload('startUpload');
		 	$('.progress', parent).show();
	  }).
	  bind('uploadProgress', function(event, file, complite, total) {
	  	var percent = complite / total * 100;
	  	$('.progress', parent).css('width', percent + '%');
	  	$(this).swfupload('uploadProgress');
	  }).
	  bind('uploadSuccess', function(file, data, response) {
	    $('.wrap', parent).append(response).show();    
	    $('.progress', parent).hide();
      serialize(parent);
	    order(parent);
	  }).
    bind('uploadError', function(file, errorCode, message) {
      alert(errorCode + ': ' + message);
    });
	  
    serialize(parent);
	  order(parent);
  })
}

function sortable(parent) {
	function serialize(parent) {
		var hash = new Array();
		$('.sortitem', parent).each(function(i) {
		  if (i != $(this).attr('pos')) {
		    hash[i] = $(this).attr('itemid');
        $(this).attr('pos', i)
		  }			
		});
		return hash;
	}

	$(parent).sortable({
		stop: function(event, ui) {
			var url = $(this).attr('action');
			if (! url) {
				url = document.location;
			} else
				url += '/';

			if (! $(this).hasClass('no-prep'))
				url += 'order';

			$.post(url, {'pos[]' : serialize($(this))});
		}
	});
}

function atlantRun() {
	$.atlantSiteMap();
  sortable($('.sortable'));
  tableInputs($('.table-inputs'));
  
  $('.address').change(function() {
    var parent = $(this).parent();
    
    function map() {
      var lon = $('#_lon').val();
      var lat = $('#_lat').val();
    
      var points = '';
      if (lon && lat) 
        points = lon+','+lat+',pm2rdm';
      
      $('.static-map', parent).remove();
      $(parent).append('<div class="static-map"><img src="http://static-maps.yandex.ru/1.x/?size=450,250&l=map&z=10&pt='+points+'" /></div>');
    }
    
    map();
    
    $(this).unbind('change').change(function() {
      $.getJSON('http://geocode-maps.yandex.ru/1.x/?geocode=' + $(this).val(), {format : 'json', sco : 'longlat'}, function(response) {
        if (response.response.GeoObjectCollection.metaDataProperty.GeocoderResponseMetaData.found > 0) {
          var pos      = response.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(' ');
          $('#_lon').val(pos[0]);
          $('#_lat').val(pos[1]);
      
          map();
        }
      })
    });
  }).each(function() {
    $(this).change();
  })
  
  $('.branches').change(function() {
    var parent = this;
    
    function map() {
      var points = '';
      $('tr:not(:first)', parent).each(function(i) {
        var lon = $('.lon', this).val();
        var lat = $('.lat', this).val();
      
        if (lon && lat) 
          points += (i > 0 ? '~' : '') + lon+','+lat+',pm2rdm';
      })
      
      $('.static-map', parent).remove();
      $(parent).append('<img class="static-map" src="http://static-maps.yandex.ru/1.x/?size=450,250&l=map&pt='+points+'" />');
    }
    
    map();
    
    $('.address', parent).unbind('change').change(function() {
      var row = $(this).parents('tr:first');
      $.getJSON('http://geocode-maps.yandex.ru/1.x/?geocode=' + $(this).val(), {format : 'json', sco : 'longlat'}, function(response) {
        if (response.response.GeoObjectCollection.metaDataProperty.GeocoderResponseMetaData.found > 0) {
          var pos      = response.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(' ');
          $('.lon', row).val(pos[0]);
          $('.lat', row).val(pos[1]);
      
          map();
        }
      })
    });
  }).each(function() {
    $(this).change();
  })
      
  $('.categories .java').click(function() {
    $('.categories .acv').removeClass('acv');
    $(this).parent().addClass('acv');
  })
  
  $('.categories .java').click(function() {
    $('.categories .acv').removeClass('acv');
    $(this).parent().addClass('acv');
    $('#category-id').val($(this).attr('rel'));
  })
  $('form').atlantInitForm();
  $('.ajax-explorer').ajaxExplorer();
  
  $('#_atlant-search').dropdown('/suggest.php?act=_atlant-search', function (object, data) {
		$('._atlant-search input').removeClass('error');
		var rows = jQuery.parseJSON(data);
		for (i = 0; i < rows.length; i++) {
			var row = rows[i];
			$(object).append('<li class="ajax" id="' + row.id + '"><a href="/admin/_search/'+row.object_model+'/'+row.object_id+'">' + row.key_words + '</a></li>');
		}
	}, function() {
		var href = $('._atlant-search .acv a').attr('href');
		if (href)
			document.location.href = $('._atlant-search .acv a').attr('href');
		else 
			$('._atlant-search input').addClass('error');
	});
  
  $('._checkbox-filter').each(function() {
    var parent = this;
            
    function serialize() {
      var list = '';      
      $('.checked, .unchecked', parent).each(function() {
        list += (list == '' ? '' : ',') + ($(this).hasClass('unchecked') ? '$' : '') + $(this).attr('name');                                 
      })    
      
      if (list == '')
        list = 'none';
      
      var url = document.location.href.replace(/filters=[^&]+&?/g, '').replace(/page=[^&]+&?/g, '').replace(/[\?&]+$/, ''); 
      
      if (list != '') {
        if (url.indexOf('?') < 0) {
          url += '?';              
        } else
          if (url[url.length-1] != '&' && url[url.length-1] != '?')         
            url += '&';           
        url += 'filters=' + list + '&page=1';
      }      
      
      url = url.replace(/[\?&]+$/, '');
      document.location.href = url;    
    }    
    
    $('label', this).click(function() {
      $('#' + $(this).attr('for')).click();
    })
    $('.three-checkbox', this).click(function() {
      if ($(this).is('.unchecked')) 
        $(this).removeClass('unchecked').removeClass('checked');
      else
      if ($(this).is('.checked')) {
        if ($(this).is('.value2'))
          $(this).addClass('unchecked');
        else
          $(this).removeClass('checked');
      } else
        $(this).addClass('checked');
        
      serialize();              
    })
  })
  
  $('.controll-list').each(function() {
  	var parent = this;
  	var controlls = $('.controlls', parent);
	  $('.item-list-controll', parent).each(function() { 
	  	var parent = this;
	  	var shift = false;
	
			function serialize(callback) {
				var list = new Array();
				$('.acv', parent).each(function(i) {
					var id = $(this).attr('itemid');
					list[i] = id;
					if (callback) {
						callback(this);
					}
				});
				return list; 
			}
			
			$(controlls).each(function() {
				var href = $(this).attr('href');
				$('.drop', this).click(function() {
					$('.acv, .current', parent).removeClass('acv').removeClass('current');
				})
				$('.public', this).click(function() {
					list = serialize(function(item) {
						$(item).removeClass('is_hidden').removeClass('acv');
					})
					$.post(href + 'show', {'list' : list})			
				})
				$('.hide', this).click(function() {
					list = serialize(function(item) {
						$(item).addClass('is_hidden').removeClass('acv');
					})
					$.post(href + 'hide', {'list' : list})			
				})
				$('.delete', this).click(function() {				
					jConfirm('Подтвердите удаление', 'Удаление', function(r) {
						if (r) {					
							list = serialize(function(item) {
								$(item).remove();
							})
							$.post(href + 'dels', {'list' : list})
							return false;
						}
					});			
				})
			})
			
	  	$(window).keydown(function(e) {
	  		if (e.keyCode == 16) {
	  			shift = true;
	  		}
	  	}).keyup(function() {
	  		shift = false;
	  	});
	  	
		  $('li', this).click(function() {
		  	$(this).toggleClass('acv');
		  	if (shift && $(this).prevAll('.current').size()) {
		  		$(this).prevUntil('.current').addClass('acv');
		  	} else 
		  	if (shift && $(this).nextAll('.current').size()) 
		  		$(this).nextUntil('.current').addClass('acv');
		  	$('.current', parent).removeClass('current');
		  	$(this).addClass('current')
		  })
		  
	    $('.layout-help').stop().html('<p>Для выделения нескольких строк зажмите <b>shift</b></p>');
	    $('.layout-help').fadeIn(function() {
	      $(this).delay(10000).fadeOut();
	    });
	  })	
  })
  
  $('.crumbs').bind('run', function() {  
    var separator = '<span class="seporator"> &rarr; </span>';
    var parent   = this;
    
    var currentId = $('input', parent).val();
    $('.crumb-' + currentId).addClass('acv');
    
  	$(document).unbind('click').click(function() {
      $('.selector', parent).hide();
    })
  
    $('.selector', parent).each(function() {
      if ($('ul', this).size() == 1) {
        $(this).addClass('col-1');
      } else {
        var col = $('li:not(li li)', this).size();
        $(this).addClass('col-' + col);
      }
    })       
  
    $('.crumb', parent).unbind('click').click(function(e) {      
      e.stopPropagation();
      
      var current = $(this);
      
      $('.selector', parent).hide();
      $('.selector', this).show();
      
      $('.java', this).unbind('click').click(function() {
        $('.selector', current).hide();
        $('.acv', parent).removeClass('acv');
        
        $(current).nextAll('.crumb, .seporator').remove();
        $(this).parents('li').each(function() {
          var title = $('span:first', this).text();
          var crumb = $('<span class="crumb">'+title+'</span>');
          $(current).after(crumb).after(separator);
          if ($('ul', this).size()) {
            $(crumb).addClass('java');
            var selector = $('<div class="selector">').append('<span class="crumb">'+title+'</span>').append($('ul:first', this).clone());
            $(crumb).append(selector);
          }
        })
        
        var id = $(this).attr('itemid');
        $('.crumb-' + id, parent).addClass('acv');
        $('input', parent).val(id);
        $(parent).trigger('run');
      })
    })
    
    $('.selector', parent).unbind('click').click(function(e) {          
      e.stopPropagation();
    })
  }).each(function() {
    $(this).trigger('run');
  })
  
  $(window).scroll(function() {
  	var scrollTop = $(window).scrollTop();
  	$('.controlls').each(function() {
  		if (! $(this).attr('offsetTop')) 
  			$(this).attr('offsetTop', $(this).offset().top)
  		if ($(this).attr('offsetTop') <= scrollTop) {
  			$(this).addClass('controlls-fixed');
  		} else
  			$(this).removeClass('controlls-fixed');
  	})
  });
  
	$('.controll-object-table').each(function() {
		var parent = this;
		var url = $(this).attr('href');

		function getProductList(callback, trigger) {
			if (! trigger)
				trigger = 'input.controll:checked';
			var list = '';
			$(trigger).each(function(i) {
				var parent = $(this).parents('tr:first');
				var id = $(parent).attr('itemid');
				list += id + ';';
				if (callback) {
					callback(parent);
				}
			});
			return list;
		}

		$('.edit-fields input:not(.controll)', parent).change(function() {
			var parent = $(this).parents('tr:first');
			var id = $(parent).attr('itemid');
			var name = $(this).attr('name');
			var value = $(this).val();
			var input = this;

			if ($(this).is('input[type=checkbox]') && ! $(this).is('input:checked')) {
				value = 0;
			}

			$.post(url + 'edit_fields', {'data[field]' : name, 'data[value]' : value, 'data[id]' : id}, function () {
				$(input).css('border', '1px solid green');
				$(input).css('padding', '1px');
			});
		});

		$('.table-dnd', parent).tableDnD({
			onDrop: function(table, row) {
				hash = new Array();
				$('tr', table).each(function(i) {
					hash[i] = $(this).attr('itemid');
				});

				$.post(url + 'order', {'pos[]' : hash});
			}
		});

		$('#select-all').change(function() {
			if (! $(this).attr('checked'))
				$('input.controll').removeAttr('checked');
			else $('input.controll').attr('checked', 'true');

			return true;
		});

		$('#delete').click(function() {
			jConfirm('Вы уверены что хотите удалить выбранные объекты?', 'Удаление', function(r) {
				if (r) {					
					var list = getProductList(function(target) {
						target.remove();
					});
					$.post(url + 'dels', {'list' : list});
					return false;
				}
			});
		});

		$('#show').click(function() {
			var list = getProductList(function(target) {
				target.removeClass('is_hidden');
			});
			$.post(url + 'show', {'list' : list});
			return false;
		});

		$('#hide').click(function() {
			var list = getProductList(function(target) {
				target.addClass('is_hidden');
			});
			$.post(url + 'hide', {'list' : list});
			return false;
		});

		var current_category_id = $('#i-current-category-id').val();

		$('#remove').click(function() {
			var popup = $('#wp-goods-remove');
			if (! $('ul', popup).size()) {
				$('.wrapper .target:first', popup).ajaxProgress();
				$.get(url + 'get_catalogue/', {}, function(data) {
					$('.wrapper .target:first', popup).html(data);
					$('a', popup).addClass('java');
					$('a', popup).click(function(){
						wpHide(popup);
						var list = getProductList(function(target) {
							target.remove();
						});
						$.post(url + 'remove', {'list' : list});
						return false;
					});
				});
			}
			wpShow(popup);
		});
	});
  
  $('._atlant-groups-fields').each(function() {
		var parent = this;
		
		var rootURL = $(this).attr('href');
		var lastFieldParentID = false;
		var isSort = false;
    
    function formRun(data, object) {
      $('.sideform .target', parent).html(data);
			$(parent).sideFormShow();
      
      $('#i-dish-restaurant-id').val($('#i-restaurant-id').val());
      
      if ($(object).hasClass('is-field-add')) {
        $('#i-dish-category').val($('a:first', $(object).parent()).text());
      }
			  
      $('.sideform form', parent).atlantInitForm().ajaxForm({
      	beforeSubmit: function() {
					$('.sideform', parent).css('opacity', '0.2');            		
      	},
	      success: function(data) {
					$('.sideform', parent).css('opacity', '1');
          
          $.get('', {}, function(data) {
            $(parent).sideFormHide(function() {
              $('._atlant-groups-fields').html($('._atlant-groups-fields', data).html());
              parent = $('._atlant-groups-fields');
              run();
            });
          })
	      }
	    }).each(function() {
	    	var form = this;
		  	$('.delete-link', this).unbind('click').click(function() {
		  		var target = this;
					jConfirm('Подтвердите удаление', 'Удаление', function(r) {
						if (r) {					
				  		$.get($(target).attr('href'), {}, function() {
			  			  $(form).remove();
                
                if ($(object).hasClass('is-group')) {
                  $(object).parents('.group:first').remove();
                } else {
					  			$(object).parent().remove();
                }
                
                $(parent).sideFormHide();
				  		})
							return false;
						}
					});
		  		return false;
		  	}); 				    	
	    })
    }
    
    function run() {  		
      $('*', parent).unbind('all');
            
  		$('.fields .wrap', parent).each(function() {
  		  var parent = this;
        $(this).sortable('destroy').sortable({
    			connectWith : '.fields .wrap',
    			start : function() {
    				lastFieldParentID = $(this).parents('.group:first').attr('itemid');
    				isSort = true;
    			},
    			update  : function(event, ui) {
    				var parentID = $(this).parents('.group:first').attr('itemid');
    				if (parentID != lastFieldParentID) {
    				  var id = $(ui.item).attr('itemid');
    					$.post($(parent).attr('action') + 'remove', {'id' : id, 'parent_id' : parentID});
              return this; 
    				}
    				
    				var hash = new Array();
    				$('.field', parent).each(function(i) {
    					hash[i] = $(this).attr('itemid');
    				});
    				$.post($(parent).attr('action') + 'order', {'pos[]' : hash});
    				isSort = false;
    			}
    		});
  		})
      
  		$('.groups', parent).each(function() {
  		  var parent = this;
  		  $(this).sortable('destroy').sortable({
    		  handle : '.handle',
    			start : function() {
    				isSort = true;
    			},
    			update : function() {
    				var hash = new Array();
    				$('.group', parent).each(function(i) {
    					hash[i] = $(this).attr('itemid');
    				});
    				$.post($(parent).attr('action') + 'order', {'pos[]' : hash});
    				isSort = false;
    			}
    		});
  		})
      
  		$(parent).sideForm(function() {
  		  $('.java', parent).unbind('click').click(function() {
  		    return false;
  		  })
  			$('.java', parent).unbind('mouseup').mouseup(function() {
          if (isSort)
  					return this;
  				var object = this;
  				$.get($(object).attr('href'), {}, function(data) {
  					formRun(data, object);
  				})
  				return this;
  			})
  		})    
    }
    
    run();
	})
  
  $('.suggest-field').each(function() {
    $(this).dropdown('/suggest.php?act=__get&table=' + $(this).attr('table') + '&fields=' + $(this).attr('fields'), function(object, data) {
      var parent = $(object).parents('.suggest');
      var fields = $(parent).attr('fields').replace(' ', '').split(',');
      console.log(fields);
  		var rows = jQuery.parseJSON(data);
      if (rows)
  		for (i = 0; i < rows.length; i++) {
  			$(object).append('<li class="ajax" id="' + rows[i].id + '">' + rows[i][fields[1]] + '</li>');
  		}
    });
  })
}

$(function() {    
  atlantRun();
})