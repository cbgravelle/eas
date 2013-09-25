/**
 * @author Adam Filkor <adam.filkor at gmail.com>
 * @created 08.05.2012 
 * @website http://filkor.org
 */

$(document).ready(function() {	
	
	initMoreInfo();
	
    if (isBrowserBad() !== true) {
		initDirectUpload();
		initDnD();
	}
});

function isBrowserBad() {
	//basic check for required features
	if (!("addEventListener" in window) || !("FileReader" in window) || !("Blob" in window) || !("FormData" in window)) {
		$("#browser-warning").fadeIn(125);
		the_return = true;
	}
	the_return = false;
	return the_return;
}


//init the little information table (slideToggle)
function initMoreInfo(){
	$("#more-info-link").click(function(){
		$("#more-info").slideToggle("fast");
	});
	
}

function initDirectUpload() {
	var fileInput = document.getElementById("filepicker-input");
	document.getElementById("direct-upload-text").onclick = function(e){
		fileInput.click();
	}
	
	fileInput.onchange = function(e) {
	//basically same as in ondrop
		$("#dropped-files").html("");
		
		var files = e.target.files;
		
		createPreviewElements(files);
		startupload(files);
		document.getElementById('ajax-loader').style.display = 'inline';
		
		//show the 'start upload' button
		/*var uploadButton = document.getElementById('uploadbutton');
		uploadButton.style.display = 'inline-block';
		
		//add an onclick property to the upload button, this will trigger the main upload process
		uploadButton.onclick = function(e){
			uploadButton.onclick = null; //disable the onclick event once it happened
			document.getElementById('ajax-loader').style.display = 'inline';
			setTimeout(function(){$('#ajax-loader').fadeOut()}, 2000);  //fade out loader after 2 sec
			startupload(files);
		};*/
	} 
}

//init Drag and Drop
function initDnD() {
	// Add drag handling to target elements
	document.getElementsByTagName("body")[0].addEventListener("dragenter", onDragEnter, false);
	document.getElementById("drop-box-overlay").addEventListener("dragleave", onDragLeave, false);
	document.getElementById("drop-box-overlay").addEventListener("dragover", noopHandler, false);
	
	// Add drop handling
	document.getElementById("drop-box-overlay").addEventListener("drop", onDrop, false);
}

function noopHandler(e) {
	e.stopPropagation();
	e.preventDefault();
}

function onDragEnter(e) {
	$("#drop-box-overlay").show();
	if (document.getElementsByTagName('body')[0].className.indexOf('easdrag') < 0) {
document.getElementsByTagName('body')[0].className += ' easdrag ';
	}
	

}

function onDragLeave(e, force) {
	/*
	 * We have to double-check the 'leave' event state because this event stupidly
	 * gets fired by JavaScript when you mouse over the child of a parent element;
	 * instead of firing a subsequent enter event for the child, JavaScript first
	 * fires a LEAVE event for the parent then an ENTER event for the child even
	 * though the mouse is still technically inside the parent bounds. If we trust
	 * the dragenter/dragleave events as-delivered, it leads to "flickering" when
	 * a child element (drop prompt) is hovered over as it becomes invisible,
	 * then visible then invisible again as that continually triggers the enter/leave
	 * events back to back. Instead, we use a 10px buffer around the window frame
	 * to capture the mouse leaving the window manually instead. (using 1px didn't
	 * work as the mouse can skip out of the window before hitting 1px with high
	 * enough acceleration).
	 */
	 force = typeof force !== 'undefined' ? force : false;
	if(force || e.pageX < 10 || e.pageY < 10 || $(window).width() - e.pageX < 10  || $(window).height - e.pageY < 10) {
		$("#drop-box-overlay").hide();

		document.getElementsByTagName('body')[0].className = document.getElementsByTagName('body')[0].className.replace('easdrag','');
	}
}

function onDrop(e) {
	// Consume the event.
	noopHandler(e);
	
	// Hide overlay
	$("#drop-box-overlay").fadeOut(0);
	//$("#drop-box-prompt").fadeOut(0);
	
	// Empty logs and preview and reset sizes
	$("#dropped-files").html("");
	
	// Get the dropped files.
	var files = e.dataTransfer.files;
	var filetype = files[0].type;
	
	// If anything is wrong with the dropped files, exit.
	if(typeof files == "undefined" || files.length == 0) {
		onDragLeave(e, true);
		uploadError('error');
		return;
	} else if (files.length > 1) {
		onDragLeave(e, true);
		uploadError('toomany')
		return;
	} else if (filetype != 'image/jpeg' && filetype != 'image/gif' && filetype != 'image/png') {
		onDragLeave(e, true);
		uploadError('notimage');
		return;
	}


	createPreviewElements(files);
	
	//show the 'start upload' button
	/*var uploadButton = document.getElementById('uploadbutton');
	uploadButton.style.display = 'inline-block';*/
	document.getElementById('ajax-loader').style.display = 'inline';
		
	startupload(files);
	

}

function uploadError(error) {
	if (error == 'error') {
		text = 'Something went wrong with the upload. Please try again or use our regular uploader.';
	} else if (error == 'toomany') {
		text = 'Please upload one work of art at a time.';
	} else if (error == 'notimage') {
		text = 'We currently only support JPEG, PNG, and GIF files with this uploader. If your art does not fit in these bounds let us know!';
	}

	$up = $('#upload-problems');

	$up.html(text);
	$up.fadeIn(250);
	setTimeout(function() { $up.fadeOut(250) }, 2000);
}


/*
The following function will generate this <li> item:
<li id="file-item-0">
	<span class="filename"></span>
	<div id="pausebutton-0" class="pauseButton small button green">Pause</div>
	<div id="progressbar-0" class="progressbar"></div>
	<div id="log-link-0" class="log-link">Open log v</div>
	<div id="log-0" class="log">#Log...<div>
</li>
*/
function createPreviewElements(files){
	this.files = files;
	
	for(var i = 0; i < this.files.length; i++) {
		
		this.fileName = this.files[i].name;
		
		//shorten long filenames
		if (this.fileName.length > 45)
			this.fileName = this.fileName.substr(0, 45) + '...';
		
		this.fileName = htmlEscape(this.fileName);
		var droppedFiles = document.getElementById('dropped-files');
		
		//create <li> item
		var item = document.createElement('li');
		item.id  = 'file-item-' + i;
		droppedFiles.appendChild(item);
		
		//create "filename"
		var filename 	   = document.createElement('span');
		filename.className = 'filename';
		filename.innerHTML = '<span id ="filestatustext">uploading</span> ' + this.fileName + '...';
		item.appendChild(filename);
		
		//create space for download link
		/*var downloadLink 	   = document.createElement('a');
		downloadLink.id 	   = 'downloadLink-' + i;
		downloadLink.className = 'downloadLink';
		downloadLink.target    = '_blank';
		item.appendChild(downloadLink);*/
		
		//add pause button
		/*var pause 		= document.createElement('div');
		pause.id		= 'pausebutton-' + i;
		pause.className = 'pauseButton small button green';
		pause.innerHTML = 'Pause';
			//custom property
			pause.uploadState = 'uploading';
		item.appendChild(pause);*/
		
		//create progressbar
		/*var progress 	   = document.createElement('div');
		progress.id 	   = 'progressbar-' + i;
		progress.className = 'progressbar';
		item.appendChild(progress);
		$("#progressbar-" + i).progressbar({ value: 0.01 }); //initalize the jquery progressbar 
	*/
		/*//create the "open log" link
		var loglink 	  = document.createElement('div');
		loglink.id  	  = 'log-link-' + i;
		loglink.className = 'log-link';
		loglink.innerHTML = 'Open log >';
		item.appendChild(loglink);
		
		//create the logger element
		var log 	  = document.createElement('div');
		log.id 		  = 'log-' + i;
		log.className = 'log';
		log.style.display = 'none';
		log.innerHTML = '#Log...<br>';
		item.appendChild(log);*/
		
		
		/*//-add event listener to to onclick to show the log
			(function(i, loglink){
				loglink.onclick = function(){
					$('#log-' + i ).slideToggle('fast');
					
					if(loglink.innerHTML == 'Close log v') {
						loglink.innerHTML = 'Open log >';
						
					} else {
						loglink.style.display = 'block';
						loglink.innerHTML = 'Close log v';	
					}
				};
			})(i, loglink);*/
		
		//Update the preview of resumed uploads, passing the elements we want to change, like progressbar, pausebutton..
		updateResumedItems(this.files[i]);
		
	//end for loop
	}
}

function startupload(files){
	document.getElementsByTagName('body')[0].className += ' easuploading';
	for(var i = 0; i<files.length; i++) {
		(function(i){
			new jsUpload(
			{
				file: files[i],

				logger: function(msg) {
					console.log(msg);
				},
				
				progressHandler: function(percent, serverFileId){
					if (percent > 0 && document.getElementsByTagName('body')[0].className.indexOf('easbitsmoving ') < 0) {
						document.getElementsByTagName('body')[0].className += ' easbitsmoving';
					}
					$("#fileprogress").css('width',percent + '%');	
				},

				processingHandler: function() {
					document.getElementById('filestatustext').innerHTML = 'processing';
					document.getElementsByTagName('body')[0].className += ' easprocessing';
				},

				imgHandler: function(imgob, id) {
					document.getElementById('filestatustext').innerHTML = 'your artwork has been uploaded';
					document.getElementById('attachmentid').value = id;
					$("#artworksubmit").removeClass('disabled');
					document.getElementById('uploaded').src = imgob[0];
					document.getElementsByTagName('body')[0].className += ' easuploaded ';
				},
				
				//pass the reference to pauseButton element 
				pauseButton: document.getElementById('pausebutton-' + i)
			});
		})(i);
	}
}

/**
 * Update the preview of resumed uploads, like fix initial progressbar value or show success tick when the upload is done before
 *
 */
function updateResumedItems(file) {
	
	var fileName  = file.name;
	var type   	  = file.type;
	var totalSize = file.size;
	
	var fileId = fileName +'|'+ type + '|' + totalSize;
	
	
	//check if it already exists in localStorage, so whether to resume uploading
	var fileData = localStorage[fileId];
	
	if (fileData) {
		var fileParts 	   = fileData.split('|');
		
		//get the timeStamp when uploaded, if older the 1 hour then delete 
		var timeUploaded   = fileParts[3]; //could be undefined
		var currentTime    = Math.round(new Date().getTime() / 1000);

		if ('undefined' != typeof timeUploaded && (currentTime - 3600) > timeUploaded) {
			localStorage.removeItem(fileId);
			return;
		}
		
		
		var currentPackage = fileParts[2]; // the third element in the array is the currentPackage number
		
		//if its already uploaded then show success image and set progressbar to 100%
		if (currentPackage == 'alldone') {
			var progressPercent = 100;
			
			//set success tick instead of pause button			
			/*var succeedImg 	= document.createElement('img');
			succeedImg.src  = 'static/succeed-tick.png';
			succeedImg.style.cssFloat = 'right';
			succeedImg.width = 40;
			
			pauseButton.parentNode.replaceChild(succeedImg, pauseButton);
			*/

			//show download link
			var serverFileId       = fileParts[0];
			/*downloadLink.innerHTML = 'Download link';
			downloadLink.href 	   = 'http://dnduploader.filkor.org/d/?id=' + serverFileId;	*/	
			console.log('easuploaded');
			document.getElementsByTagName('body')[0].className += ' easuploaded ';
		} else {
			//else if not uploaded the whole then get the current package number, and return the percent		
			var packetSize 	    = 512 * 512; //bytes, should be a global value in reality
			var totalPackages   = Math.ceil(totalSize / packetSize);
			
			var progressPercent =  (currentPackage / totalPackages) * 100;			
		}
		
		//some sugar to animate progressbar, and FIX its right corner
		/*$(progressElement).find('.ui-progressbar-value').addClass('ui-corner-right').stop(true).animate({
			width: progressPercent + '%'
		}, 400);*/	
		
		
	}
}


function htmlEscape(str) {
    return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
}
