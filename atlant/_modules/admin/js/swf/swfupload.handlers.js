
function solo_fileQueueError(fileObj, error_code, message) {
	try {
		var error_name = "";
		switch(error_code) {
			case SWFUpload.ERROR_CODE_QUEUE_LIMIT_EXCEEDED:
				error_name = "You have attempted to queue too many files.";
			break;
		}

		if (error_name !== "") {
			alert(error_name);
			return;
		}

		switch(error_code) {
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				image_name = "zerobyte.gif";
			break;
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				image_name = "toobig.gif";
			break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			default:
				alert(message);
				image_name = "error.gif";
			break;
		}

		AddImage("images/" + image_name);

	} catch (ex) { this.debug(ex); }

}

function solo_fileDialogComplete(num_files_queued) {
	try {
		if (num_files_queued > 0) {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function solo_uploadProgress(fileObj, bytesLoaded) {

	try {
		var percent = Math.ceil((bytesLoaded / fileObj.size) * 100)
		var progress = new FileProgressSolo(fileObj,  this.customSettings.upload_target);
		progress.SetProgress(percent);
		if (percent === 100) {
			progress.SetStatus("Создание эскиза...");
			progress.ToggleCancel(false);
			progress.ToggleCancel(true, this, fileObj.id);
		} else {
			progress.SetStatus("Загрузка...");
			progress.ToggleCancel(true, this, fileObj.id);
		}
	} catch (ex) { this.debug(ex); }
}

function solo_uploadSuccess(fileObj, server_data) {
	try {
		// upload.php returns the thumbnail id in the server_data, use that to retrieve the thumbnail for display

		AddImage("thumbnail.php?id=" + server_data, this.customSettings.upload_target);

		var progress = new FileProgressSolo(fileObj,  this.customSettings.upload_target);

		progress.SetStatus("Эскиз создан.");
		progress.ToggleCancel(false);


	} catch (ex) { this.debug(ex); }
}

function solo_uploadComplete(fileObj) {
	try {
		/*  I want the next upload to continue automatically so I'll call startUpload here */
		if (this.getStats().files_queued > 0) {
			this.startUpload();
		} else {
			var progress = new FileProgressSolo(fileObj,  this.customSettings.upload_target);
			progress.SetComplete();
			progress.SetStatus("Загружена.");
			progress.ToggleCancel(false);
		}
	} catch (ex) { this.debug(ex); }
}

function solo_uploadError(fileObj, error_code, message) {
	var image_name =  "error.gif";
	try {
		switch(error_code) {
			case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
				try {
					var progress = new FileProgressSolo(fileObj,  this.customSettings.upload_target);
					progress.SetCancelled();
					progress.SetStatus("Stopped");
					progress.ToggleCancel(true, this, fileObj.id);
				}
				catch (ex) { this.debug(ex); }
			case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
				image_name = "uploadlimit.gif";
			break;
			default:
				alert(message);
			break;
		}

		AddImage("images/" + image_name, this.customSettings.upload_target);

	} catch (ex) { this.debug(ex); }

}


/* ******************************************
 *	FileProgressSolo Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgressSolo(fileObj, target_id) {
	this.file_progress_id = "divFileProgressSolo"+target_id;

	this.FileProgressSoloWrapper = document.getElementById(this.file_progress_id);
	if (!this.FileProgressSoloWrapper) {
		this.FileProgressSoloWrapper = document.createElement("div");
		this.FileProgressSoloWrapper.className = "progressWrapper";
		this.FileProgressSoloWrapper.id = this.file_progress_id;

		this.FileProgressSoloElement = document.createElement("div");
		this.FileProgressSoloElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(fileObj.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.FileProgressSoloElement.appendChild(progressCancel);
		this.FileProgressSoloElement.appendChild(progressText);
		this.FileProgressSoloElement.appendChild(progressStatus);
		this.FileProgressSoloElement.appendChild(progressBar);

		this.FileProgressSoloWrapper.appendChild(this.FileProgressSoloElement);

		document.getElementById(target_id).appendChild(this.FileProgressSoloWrapper);
		FadeIn(this.FileProgressSoloWrapper, 0);

	} else {
		this.FileProgressSoloElement = this.FileProgressSoloWrapper.firstChild;
		this.FileProgressSoloElement.childNodes[1].firstChild.nodeValue = fileObj.name;
	}

	this.height = this.FileProgressSoloWrapper.offsetHeight;

}
FileProgressSolo.prototype.SetProgress = function(percentage) {
	this.FileProgressSoloElement.className = "progressContainer green";
	this.FileProgressSoloElement.childNodes[3].className = "progressBarInProgress";
	this.FileProgressSoloElement.childNodes[3].style.width = percentage + "%";
}
FileProgressSolo.prototype.SetComplete = function() {
	this.FileProgressSoloElement.className = "progressContainer blue";
	this.FileProgressSoloElement.childNodes[3].className = "progressBarComplete";
	this.FileProgressSoloElement.childNodes[3].style.width = "";

}
FileProgressSolo.prototype.SetError = function() {
	this.FileProgressSoloElement.className = "progressContainer red";
	this.FileProgressSoloElement.childNodes[3].className = "progressBarError";
	this.FileProgressSoloElement.childNodes[3].style.width = "";

}
FileProgressSolo.prototype.SetCancelled = function() {
	this.FileProgressSoloElement.className = "progressContainer";
	this.FileProgressSoloElement.childNodes[3].className = "progressBarError";
	this.FileProgressSoloElement.childNodes[3].style.width = "";

}
FileProgressSolo.prototype.SetStatus = function(status) {
	this.FileProgressSoloElement.childNodes[2].innerHTML = status;
}

FileProgressSolo.prototype.ToggleCancel = function(show, upload_obj, file_id) {
	this.FileProgressSoloElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (upload_obj) {
		this.FileProgressSoloElement.childNodes[0].onclick = function() { upload_obj.cancelUpload(); return false; };
	}
}
function argItems (theArgName) {
	sArgs = location.search.slice(1).split('&');
    r = '';
    for (var i = 0; i < sArgs.length; i++) {
        if (sArgs[i].slice(0,sArgs[i].indexof('=')) == theArgName) {
            r = sArgs[i].slice(sArgs[i].indexOf('=')+1);
            break;
        }
    }
    return (r.length > 0 ? unescape(r).split(',') : '')
}

function explode( delimiter, string ) {    // Split a string by string
    //
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: kenneth
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

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

function AddImage(src, target) {

	src = explode('&tmp_name=',src);
	src = explode('&file_name=',src[1]);
	var tmp_name = decodeURIComponent(src[0]);
	var file_name = decodeURIComponent(src[1]);
	$("#swf_file_"+target).html('<input type="hidden" name="data['+target+'][img_file][tmp_name]" value="'+tmp_name+'"><br><input type="hidden" name="data['+target+'][img_file][name]" value="'+file_name+'">');

}

function FadeIn(element, opacity) {
	var reduce_opacity_by = 15;
	var rate = 30;	// 15 fps


	if (opacity < 100) {
		opacity += reduce_opacity_by;
		if (opacity > 100) opacity = 100;

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
			}
		} else {
			element.style.opacity = opacity / 100;
		}
	}

	if (opacity < 100) {
		setTimeout(function() { FadeIn(element, opacity); }, rate);
	}
}


/* This is an example of how to cancel all the files queued up.  It's made somewhat generic.  Just pass your SWFUpload
object in to this method and it loops through cancelling the uploads. */
function cancelQueue(instance) {
	document.getElementById(instance.customSettings.cancelButtonId).disabled = true;
	instance.stopUpload();
	var stats;

	do {
		stats = instance.getStats();
		instance.cancelUpload();
	} while (stats.files_queued !== 0);

}


/* This is an example of how to cancel all the files queued up.  It's made somewhat generic.  Just pass your SWFUpload
object in to this method and it loops through cancelling the uploads. */
function cancelQueue(instance) {
	//document.getElementById(instance.customSettings.cancelButtonId).disabled = true;
	instance.stopUpload();
	var stats;

	do {
		stats = instance.getStats();
		instance.cancelUpload();
	} while (stats.files_queued !== 0);

}

/* **********************
   Event Handlers
   These are my custom event handlers to make my
   web application behave the way I went when SWFUpload
   completes different tasks.  These aren't part of the SWFUpload
   package.  They are part of my application.  Without these none
   of the actions SWFUpload makes will show up in my application.
   ********************** */
function fileDialogStart() {
	/* I don't need to do anything here */
}
function fileQueued(file) {
	try {
		// You might include code here that prevents the form from being submitted while the upload is in
		// progress.  Then you'll want to put code in the Queue Complete handler to "unblock" the form
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("Подготовка...");
		progress.toggleCancel(true, this);

	} catch (ex) {
		this.debug(ex);
	}

}

function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus("File is too big.");
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus("Cannot upload Zero Byte files.");
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus("Invalid File Type.");
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
			alert("You have selected too many files.  " +  (message > 1 ? "You may only add " +  message + " more files" : "You cannot add any more files."));
			break;
		default:
			if (file !== null) {
				progress.setStatus("Unhandled Error");
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (this.getStats().files_queued > 0) {
			document.getElementById(this.customSettings.cancelButtonId).disabled = false;
		}

		/* I want auto start and I can do that here */
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}

function uploadStart(file) {
	try {
		/* I don't want to do any file validation or anything,  I'll just update the UI and return true to indicate that the upload should start */
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("Загрузка...");
		progress.toggleCancel(true, this);
	}
	catch (ex) {
	}

	return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {

	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus("Загрузка...");
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setComplete();
		progress.setStatus("Завершена.");
		progress.toggleCancel(false);

	} catch (ex) {
		this.debug(ex);
	}
}

function uploadComplete(file) {
	try {
		/*  I want the next upload to continue automatically so I'll call startUpload here */
		if (this.getStats().files_queued === 0) {

			document.getElementById(this.customSettings.cancelButtonId).disabled = true;

		} else {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}

}

function uploadError(file, errorCode, message) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("Upload Error: " + message);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
			progress.setStatus("Configuration Error");
			this.debug("Error Code: No backend file, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("Upload Failed.");
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("Server (IO) Error");
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("Security Error");
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("Upload limit exceeded.");
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:
			progress.setStatus("File not found.");
			this.debug("Error Code: The file was not found, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("Failed Validation.  Upload skipped.");
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			if (this.getStats().files_queued === 0) {
				document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			}
			progress.setStatus("Cancelled");
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("Stopped");
			break;
		default:
			progress.setStatus("Unhandled Error: " + error_code);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}