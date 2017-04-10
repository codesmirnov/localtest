(function($) {

	// If this option is `false` then code hightlight disabled
	elRTE.prototype.codeMirror = true;

	elRTE.prototype.updateSourceOld = elRTE.prototype.updateSource;
	elRTE.prototype.updateSource = function() {
		if( this.codeMirror )	{

			// HTML block elements
			var blockHtmlElements = 'h[1-6]|div|p|blockquote|pre|form|label|select|input|button|ol|ul|dd|dt|li|table|thead|tbody|td|th|tr';

			var html = this.filter.source($(this.doc.body).html());
			html = html	.replace(new RegExp('(</?(' + blockHtmlElements + ')[^<]*?>)\\s*', 'gi'), '$1\n')
						.replace(new RegExp('\\s*(</?(' + blockHtmlElements + ')[^<]*?>)', 'gi'), '\n$1');
			this.source.val(html);

			// Codemirror settings
			this.codeMirror = CodeMirror.fromTextArea(this.source[0], {
				height: "100%",
				parserfile: "parsexml.js",
				stylesheet: "/_modules/admin/js/elrte-1.2/js/codemirror/css/xmlcolors.css",
				path: "/_modules/admin/js/elrte-1.2/js/codemirror/js/",
				reindentOnLoad: true,
				//lineNumbers: true,
				indentUnit: 5
			});
			this.source	.css('display','block')
						.css('position','absolute')
						.css('z-index','-1');
		}
		else	{
			this.updateSourceOld();
		}
	}

	elRTE.prototype.updateEditorOld = elRTE.prototype.updateEditor;
	elRTE.prototype.updateEditor = function() {
		if( this.codeMirror )	{
			this.codeMirror.toTextArea();
			this.source.css('display','none');
		}
		this.updateEditorOld();
	}


})(jQuery);