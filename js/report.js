
// Startup variables
var imageTag = false;
var theSelection = false;
var frmName = 'l_report';

var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;

var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);

// Helpline messages
b_help = "Жирный текст: [b]текст[/b]  (alt+b)";
i_help = "Наклонный текст: [i]текст[/i]  (alt+i)";
u_help = "Подчёркнутый текст: [u]текст[/u]  (alt+u)";
p_help = "Вставить картинку: [img]http://image_url[/img]  (alt+p)";
w_help = "Вставить ссылку: [url]http://url[/url] или [url=http://url]текст ссылки[/url]  (alt+w)";

function formcheck() {
	if (document.forms[frmName].elements['key'].value == '') {
		alert ('Укажите хотя бы одно место из тех, что вы посетили!');
		return false;
	}
	if (document.forms[frmName].elements['gr'].value == '') {
		alert ('Вы забыли указать длительность вашего путешествия!');
		return false;
	}
	if (document.forms[frmName].elements['message'].value == '') {
		alert ('А где же текст отчета?');
		return false;
	}
	return true;
}


// Define the bbCode tags
rcode = new Array();
rtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[img]','[/img]','[url]','[/url]');
imageTag = false;

// Shows the help messages in the helpline window
function helpline(help) {
	document.forms[frmName].helpbox.value = eval(help + "_help");
}


// Replacement for arrayname.length property
function getarraysize(thearray) {
	for (i = 0; i < thearray.length; i++) {
		if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
			return i;
		}
	return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
	thearray[ getarraysize(thearray) ] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
	thearraysize = getarraysize(thearray);
	retval = thearray[thearraysize - 1];
	delete thearray[thearraysize - 1];
	return retval;
}


function rfontstyle(ropen, rclose) {
	var txtarea = document.forms[frmName].elements['message'];

	if ((clientVer >= 4) && is_ie && is_win) {
		theSelection = document.selection.createRange().text;
		if (!theSelection) {
			txtarea.value += ropen + rclose;
			txtarea.focus();
			return;
		}
		document.selection.createRange().text = ropen + theSelection + rclose;
		txtarea.focus();
		return;
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, ropen, rclose);
		return;
	}
	else
	{
		txtarea.value += ropen + rclose;
		txtarea.focus();
	}
	storeCaret(txtarea);
}


function rstyle(rnumber) {
	var txtarea = document.forms[frmName].elements['message'];

	txtarea.focus();
	donotinsert = false;
	theSelection = false;
	rlast = 0;

	if (rnumber == -1) { // Close all open tags & default button names
		while (rcode[0]) {
			butnumber = arraypop(rcode) - 1;
			txtarea.value += rtags[butnumber + 1];
			buttext = eval('document.forms[frmName].addrcode' + butnumber + '.value');
			eval('document.forms[frmName].addrcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
		}
		imageTag = false; // All tags are closed including image tags :D
		txtarea.focus();
		return;
	}

	if ((clientVer >= 4) && is_ie && is_win)
	{
		theSelection = document.selection.createRange().text; // Get text selection
		if (theSelection) {
			// Add tags around selection
			document.selection.createRange().text = rtags[rnumber] + theSelection + rtags[rnumber+1];
			txtarea.focus();
			theSelection = '';
			return;
		}
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, rtags[rnumber], rtags[rnumber+1]);
		return;
	}

	// Find last occurance of an open tag the same as the one just clicked
	for (i = 0; i < rcode.length; i++) {
		if (rcode[i] == rnumber+1) {
			rlast = i;
			donotinsert = true;
		}
	}

	if (donotinsert) {		// Close all open tags up to the one just clicked & default button names
		while (rcode[rlast]) {
				butnumber = arraypop(rcode) - 1;
				txtarea.value += rtags[butnumber + 1];
				buttext = eval('document.forms[frmName].addrcode' + butnumber + '.value');
				eval('document.forms[frmName].addrcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
				imageTag = false;
			}
			txtarea.focus();
			return;
	} else { // Open tags

		if (imageTag && (rnumber != 6)) {		// Close image tag before adding another
			txtarea.value += rtags[7];
			lastValue = arraypop(rcode) - 1;	// Remove the close image tag from the list
			document.forms[frmName].addrcode6.value = "Img";	// Return button back to normal state
			imageTag = false;
		}

		// Open tag
		txtarea.value += rtags[rnumber];
		if ((rnumber == 6) && (imageTag == false)) imageTag = 1; // Check to stop additional tags after an unclosed image tag
		arraypush(rcode,rnumber+1);
		eval('document.forms[frmName].addrcode'+rnumber+'.value += "*"');
		txtarea.focus();
		return;
	}
	storeCaret(txtarea);
}

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	if (selEnd == 1 || selEnd == 2)
		selEnd = selLength;

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);
	txtarea.value = s1 + open + s2 + close + s3;
	return;
}

// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}


