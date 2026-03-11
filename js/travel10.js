function roll(item,value) {
 item.innerHTML=value;
}

function readIMG(input) {
  if (input.files && input.files[0]) {
    $('#imgpreview').hide();    
    $('#imgsubmit').hide();    
    var reader = new FileReader();
    reader.onloadend = function () {
      $('#imgpreview').attr('src', reader.result) .width('auto') .height(200);
      $('#imgpreview').show();    
      $('#imgsubmit').show();    
    }
    reader.readAsDataURL(input.files[0]);
  }
}

  function pseudo_ajax_form(formname, elementname, tochangename, showname) {
    var iiframe;
    var $form = $(formname);
    if (!$form.attr('target')) {
      //create a unique iframe for the form
      iiframe = $("<iframe></iframe>").attr('name', 'pseudo_ajax_form_' + Math.floor(Math.random() * 999999)).hide().appendTo($('body'));
      $form.attr('target', iiframe.attr('name'));
    }
    else {
      iiframe = $('iframe[name=" ' + $form.attr('target') + ' "]');
    }
    iiframe.load(function () {
      //get the server response
      var response = $(elementname, iiframe.contents());
      if (response) {
        var htmc = response.html();
        iiframe.contents().find('body').text('');
        $(tochangename).html(htmc);
      }
      else {
        alert(iiframe.contents().find('body').text());
        iiframe.contents().find('body').text('');
      }
      $(showname).show();
    });
  }

function ChTjs(itemname,parent) {
 var c=parent.getElementsByTagName('img')[0];
 var i=c.getAttribute('alt');
 if (i=='>') {
  if ($("#o-"+itemname).text().length<10) {   
   c.setAttribute('src','/jpg/spin6.gif');
   c.setAttribute('alt','Load...');
   $("#o-"+itemname).load('/otkliksmall.php/'+itemname, function() {
      $(this).show();
      c.setAttribute('src','/jpg/minus.gif');
      c.setAttribute('alt','V');
   });
  }
  else {
   $("#o-"+itemname).show();
   c.setAttribute('src','/jpg/minus.gif');
   c.setAttribute('alt','V');
  }
 }
 else {
  $("#o-"+itemname).hide();
  c.setAttribute('src','/jpg/plus.gif');
  c.setAttribute('alt','>');
 }
}


function ChTjsBs(itemname,parent) {
 var c=parent.getElementsByTagName('img')[0];
 var i=c.getAttribute('alt');
 if (i=='>') {
  if ($("#o-"+itemname).text().length<10) {   
   c.setAttribute('src','/jpg/spin6.gif');
   c.setAttribute('alt','Load...');
   $("#o-"+itemname).load('/otkliksml_bs.php/'+itemname, function() {
      $(this).show();
      c.setAttribute('src','/jpg/minus.gif');
      c.setAttribute('alt','V');
   });
  }
  else {
   $("#o-"+itemname).show();
   c.setAttribute('src','/jpg/minus.gif');
   c.setAttribute('alt','V');
  }
 }
 else {
  $("#o-"+itemname).hide();
  c.setAttribute('src','/jpg/plus.gif');
  c.setAttribute('alt','>');
 }
}

function ChToggle(itemname,parent) {
  var c = document.getElementById(itemname);
  ChElem(c,parent);
}

function ChElemV(c,parent) {
   c.style.visibility = 'visible';
   c.style.display = 'block';
   if (parent) {
    parent.getElementsByTagName('img')[0].setAttribute('src','/jpg/minus.gif');
    parent.getElementsByTagName('img')[0].setAttribute('alt','V');
   }
}

function ChElemU(c,parent) {
   c.style.visibility = 'hidden';
   c.style.display = 'none';
   if (parent) {
    parent.getElementsByTagName('img')[0].setAttribute('src','/jpg/plus.gif');
    parent.getElementsByTagName('img')[0].setAttribute('alt','>');
   }
}

function ChElem(c,parent) {
  if (c.style.display == 'none') {
   ChElemV(c,parent)
  }
  else {
   ChElemU(c,parent)
  }
}

function AllToggle(itemname,parent) {
 var i=parent.getElementsByTagName('img')[0].getAttribute('alt');
 var x;
 var c=document.getElementById(itemname);
 var t=c.getElementsByTagName("tbody")[0]
 var m;
 var divs=t.getElementsByTagName("td");

 for(x=0;x<divs.length;x++) {
  c=divs[x];
  if (m=c.getElementsByTagName('div')[0]) {
   if (m.title=="CHG") {
    if (i=='V') {
     ChElemU(m,c.getElementsByTagName('a')[1]);
    }
    else {
     ChElemV(m,c.getElementsByTagName('a')[1]);
    }
   }
  }
 }
 if (i=='V') {
   parent.getElementsByTagName('img')[0].setAttribute('src','/jpg/plus.gif');
   parent.getElementsByTagName('img')[0].setAttribute('alt','>');
 }
 else {
   parent.getElementsByTagName('img')[0].setAttribute('src','/jpg/minus.gif');
   parent.getElementsByTagName('img')[0].setAttribute('alt','V');
 }
}

function ChOpen(itemname)
{
  if (document.getElementById)
  {
    var currentitem = document.getElementById(itemname);
  }
  else if (document.all)
  {
    var currentitem = document.all[itemname];
  }
  else if (document.layers)
  {
    var currentitem = document.layers[itemname];
  }
  else {
   return;
  }
  currentitem.style.visibility = 'visible';
  currentitem.style.display = 'block';
}

function ChHide(itemname)
{
  if (document.getElementById)
  {
    var currentitem = document.getElementById(itemname);
  }
  else if (document.all)
  {
    var currentitem = document.all[itemname];
  }
  else if (document.layers)
  {
    var currentitem = document.layers[itemname];
  }
  else {
   return;
  }
  currentitem.style.visibility = 'hidden';
  currentitem.style.display = 'none';
}


function ChVis(currentelement,itemname)
{
  if (document.getElementById)
  {
    var currentitem = document.getElementById(itemname);
  }
  else if (document.all)
  {
    var currentitem = document.all[itemname];
  }
  else if (document.layers)
  {
    var currentitem = document.layers[itemname];
  }
  else {
   return;
  }
  if (currentelement.checked == true ) {
    currentitem.style.visibility = 'hidden';
    currentitem.style.display = 'none';
  }
  else {
    currentitem.style.visibility = 'visible';
    currentitem.style.display = 'block';
  }
}

function ChUnVis(currentelement,itemname)
{
  if (document.getElementById)
  {
    var currentitem = document.getElementById(itemname);
  }
  else if (document.all)
  {
    var currentitem = document.all[itemname];
  }
  else if (document.layers)
  {
    var currentitem = document.layers[itemname];
  }
  else {
   return;
  }
  if (currentelement.checked == true ) {
    currentitem.style.visibility = 'visible';
    currentitem.style.display = 'block';
  }
  else {
    currentitem.style.visibility = 'hidden';
    currentitem.style.display = 'none';
  }
}

function ViewImg(alb) {
 var MyAnc="/phalbum.php" + alb;
 MyWin=window.open(MyAnc, '','scrollbars=yes,resizable=yes,width=950,height=750');
 return true;
}

function ViewFoto(alb,foto) {
 var MyAnc="/foto/" + alb + "/" + foto + ".jpg";
 MyWin=window.open(MyAnc, '','scrollbars=yes,resizable=yes,width=950,height=750');
 return true;
}

function ViewPFoto(id,foto) {
 var MyAnc="/photo/" + id + "/" + foto + ".jpg";
 MyWin=window.open(MyAnc, '','scrollbars=yes,resizable=yes,width=750,height=750');
 return true;
}

function AddCoord(id) {
 var MyAnc="/add_coord.php?id=" + id;
 MyWin=window.open(MyAnc, '','scrollbars=yes,resizable=yes,width=450,height=450');
 MyWin.focus();
 return true;
}

function ChDate(h) {
 a = h.value.replace(" ","");
 if (a != "") {
  b = new Date(a.substr(6,4),a.substr(3,2)-1,a.substr(0,2));
  d = b.getDate();
  if (d<10) d = "0"+d.toString();
  m = b.getMonth()+1;
  if (m<10) m = "0"+m.toString();
  if ((a != d+"."+m+"."+b.getFullYear())||((a.length != 10))) {
   alert("Формат даты должен быть: DD.MM.YYYY, например 31.12.2004");
   h.focus();
   return false;
  }
 }
}

function SelId(form,field,proc) {
 var fr=document.forms[form];
 var fl=fr[field];
 var fv=fl.value;
 var MyAnc = proc + "?form=" + form + "&field=" + field + "&find=" + fv;
 MyWin=window.open(MyAnc, 'Найти','scrollbars=yes,resizable=yes,width=600,height=250');
 MyWin.focus();
 return true;
}

function NewSelId(form,field,proc) {
 var fr=document.forms[form];
 var MyAnc = proc + "?o=" + form + "&i=" + field;
 MyWin=window.open(MyAnc, 'Найти','scrollbars=yes,resizable=yes,width=600,height=250');
 MyWin.focus();
 return true;
}

function XSelId(form,field,proc,xid) {
 var fr=document.forms[form];
 var MyAnc = proc + "?o=" + form + "&i=" + field+ "&x=" + xid;
 MyWin=window.open(MyAnc, 'Найти','scrollbars=yes,resizable=yes,width=600,height=250');
 MyWin.focus();
 return true;
}

function NewSelExId(form,field,proc) {
 var fr=document.forms[form];
 var fl=fr[field];
 var ex=fl.value;
 var MyAnc = proc + "?o=" + form + "&id=" + ex + "&f=" + field;
 MyWin=window.open(MyAnc, 'Найти','scrollbars=yes,resizable=yes,width=600,height=250');
 MyWin.focus();
 return true;
}

function NewClrId(form,field) {
 var fr=document.forms[form];
 var fl=fr[field];
 fl.value='';
 var flo=fr['help' + field];
 flo.value = '';
 return true;
}

function NewAddId(form,field) {
 var vf=document.forms['find'];
 var ff=vf['id'];
 var id=ff.value;
 var td=ff.options[ff.selectedIndex].text;
 var fr=window.opener.document.forms[form];
 var fl=fr[field];
 fl.value=id;
 var flo=fr['help' + field];
 flo.value = td;
 window.close();
 var subm=fr['autosubmit'].value;
 if (subm == 'yes') {
  fr.submit();
 }
 return true;
}

function XAddId(form,field,id,td) {
 var fr=window.opener.document.forms[form];
 var fl=fr[field];
 fl.value=id;
 var flo=fr['help' + field];
 flo.value = td;
 window.close();
 var subm=fr['autosubmit'].value;
 if (subm == 'yes') {
  fr.submit();
 }
 return true;
}

function AddId(form,field) {
 var vf=document.forms['find'];
 var ff=vf['id'];
 var id=ff.value;
 var td=ff.options[ff.selectedIndex].text;
 var fr=window.opener.document.forms[form];
 var fl=fr[field];
 fl.value=id;
 var flo=fr['help' + field];
 flo.value=td;
 fl.focus();
 window.close();
 return true;
}

function AddExId(form,field) {
 var vf=document.forms['find'];
 var ff=vf['id'];
 var id=ff.value;
 var td=ff.options[ff.selectedIndex].text;
 var fr=window.opener.document.forms[form];
 var fl=fr['ex' + field];
 fl.value=id;
 var flo=fr['helpex' + field];
 flo.value=td;
 fl.focus();
 window.close();
 return true;
}

function AddIdN(form,field) {
 var vf=document.forms['find'];
 var ff=vf['id'];
 var id=ff.value;
 var td=ff.options[ff.selectedIndex].text;
 var fr=window.opener.document.forms[form];
 var fl=fr[field];
 fl.value=td;
 var flo=fr[field + 'id'];
 flo.value=id;
 fl.focus();
 window.close();
 return true;
}

function divreplace(divID,vZoom,setText){
 var v1='<iframe name=gmap width="525" height="500" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=ru&amp;geocode=&amp;q=http:%2F%2Fautotravel.ru%2Fkml.php%2F';
 var v2='.kml&amp;ie=UTF8&amp;t=h&amp;z=';
 var v3='&amp;output=embed"></iframe>';
 document.getElementById(divID).innerHTML = v1 + setText + v2 + vZoom + v3;
}

function divreplace_r(divID,setText){
 var v1='<iframe name=gmap width="725" height="500" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=ru&amp;geocode=&amp;q=http:%2F%2Fautotravel.ru%2Fkml_r.php%3F';
 var v2='&amp;ie=UTF8&amp;output=embed"></iframe>';
 document.getElementById(divID).innerHTML = v1 + setText + v2;
}

function BasketAdd(obj,basket,srcid) {
    var iframe=document.getElementById("basket");
    if (iframe) {
      var added = geturl('/basket_w.php?a=add&o=' + obj +'&id=' + basket);
      if (added) {
       iframe.innerHTML=added;
       var src=document.getElementById(srcid);
       src.innerHTML="{Добавлен}";
       src.click= null;
      }
    }
}

function geturl(filename) {
 oxmlhttp = null;
 try {
  oxmlhttp = new XMLHttpRequest();
  oxmlhttp.overrideMimeType("text/xml");
 }
 catch(e) {
   try {
     oxmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
   }
   catch(e) {
     return null;
   }
 }

 if (!oxmlhttp) return null;
 try {
   oxmlhttp.open("GET",filename,false);
   oxmlhttp.send(null);
 }
 catch(e) {
   return null;
 }
 return oxmlhttp.responseText;
}

function initmapv2(kmlfile,parent,lat,lng) {
  var mapdiv = document.getElementById("gmapv3");
  if (parent != null) {
   var c=parent.getElementsByTagName('img')[0];
  }
  if (mapdiv.style.display == 'none') {
   mapdiv.style.display = 'block';
   if (mapdiv.style.visibility != 'visible') {
    mapdiv.style.visibility = 'visible';
   }
   if (parent != null) {
    c.setAttribute('src','/jpg/minus.gif');
    c.setAttribute('alt','V');
   }
   var map = new L.Map('gmapv3', {center: new L.LatLng(lat, lng), zoom: 12});
   map.options.maxZoom = 17;
   //var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
   var yndx = new L.Yandex();
   var ysat = new L.Yandex("hybrid");
   var pois = new L.KML(kmlfile, {async: true});
   pois.on("loaded", function(e) {
    map.fitBounds(e.target.getBounds());
   });
   map.addLayer(pois);
   //map.addLayer(osm);
   map.addLayer(yndx);
   map.addControl(new L.Control.Layers({"Карта":yndx, "Спутник":ysat}));
  }
  else {
   if (parent != null) {
    mapdiv.style.display = 'none';
    c.setAttribute('src','/jpg/plus.gif');
    c.setAttribute('alt','>');
   }
  }
}

function initmapv1(kmlfile,parent,lat,lng) {
  var mapdiv = document.getElementById("gmapv3");
  if (parent != null) {
   var c=parent.getElementsByTagName('img')[0];
  }
  if (mapdiv.style.display == 'none') {
   mapdiv.style.display = 'block';
   if (mapdiv.style.visibility != 'visible') {
    mapdiv.style.visibility = 'visible';
   }
   if (parent != null) {
    c.setAttribute('src','/jpg/minus.gif');
    c.setAttribute('alt','V');
   }
   var map = new L.Map('gmapv3', {center: new L.LatLng(lat, lng), zoom: 12});
   var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
   var pois = new L.KML(kmlfile, {async: true});
   pois.on("loaded", function(e) {
    map.fitBounds(e.target.getBounds());
   });
   map.addLayer(pois);
   map.addLayer(osm);
  }
  else {
   if (parent != null) {
    mapdiv.style.display = 'none';
    c.setAttribute('src','/jpg/plus.gif');
    c.setAttribute('alt','>');
   }
  }
}

function initmapv3(kmlfile,parent,lat,lng) {
  var options = {
    center: new google.maps.LatLng(lat,lng),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var mapdiv = document.getElementById("gmapv3");
  if (parent != null) {
   var c=parent.getElementsByTagName('img')[0];
  }
  if (mapdiv.style.display == 'none') {
   mapdiv.style.display = 'block';
   if (mapdiv.style.visibility != 'visible') {
    mapdiv.style.visibility = 'visible';
    var map = new google.maps.Map(mapdiv, options);
    var parser = new geoXML3.parser({map: map, processStyles: true, singleInfoWindow:true});
    var bounds = new google.maps.LatLngBounds();
    parser.parse(kmlfile);
    map.fitBounds(bounds);
    var listenx = google.maps.event.addListener(map, "idle", function() { 
     if (map.getZoom() > 14) map.setZoom(14); 
     google.maps.event.removeListener(listenx); 
    });
   }
   if (parent != null) {
    c.setAttribute('src','/jpg/minus.gif');
    c.setAttribute('alt','V');
   }
  }
  else {
   if (parent != null) {
    mapdiv.style.display = 'none';
    c.setAttribute('src','/jpg/plus.gif');
    c.setAttribute('alt','>');
   }
  }
}

function ShowAllImg() {
  $('.atcbox').each(function () {
   var count = $(this).children().length;
   if (count == 0) {
      var href = $(this).attr('href');
      var small = href.substr(0,href.lastIndexOf("."));
      var img = document.createElement('img');
      img.src=small + '-s.jpg';
      this.appendChild(img);
    }
   });  
}

function initmapv5(kmlfile, mapdiv) {
  var options = {
      center: new google.maps.LatLng(0,0),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: true,
      navigationControl: true,
      gestureHandling: 'greedy',
      draggingCursor: 'move'
  };
  mapdiv.style.visibility = 'visible';
  mapdiv.style.display = 'block';
  map = new google.maps.Map(mapdiv, options);
  parser = new KmlMapParser({ map: map,
      zoomOnClick: false,
      kml: kmlfile,
      showSidebar: true,
      showFolders: true,
      showSidebarDescriptions: true,
      showSidebarBubble: true,
      showRootName: true,
      allFoldersOpen: false
  });
  var bounds = new google.maps.LatLngBounds();
  //parser.parse(kmlfile);
  map.fitBounds(bounds);
  var listenx = google.maps.event.addListener(map, "idle", function() { 
    if (map.getZoom() > 14) map.setZoom(14); 
    google.maps.event.removeListener(listenx); 
  });
}

function initmapv6(kmlfile,mapdiv) {
  var options = {
      center: new google.maps.LatLng(0,0),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: true,
      navigationControl: true,
      gestureHandling: 'greedy',
      draggingCursor: 'move'
  };
  mapdiv.style.visibility = 'visible';
  mapdiv.style.display = 'block';
  map = new google.maps.Map(mapdiv, options);
  parser = new KmlMapParser({ map: map,
      afterParseFn: afterParse,
      zoomOnClick: false,
      kml: kmlfile,
      sidebarId: 'the_side_bar',
      showSidebar: true,
      showFolders: true,
      showSidebarDescriptions: true,
      showSidebarBubble: true,
      showRootName: true,
      allFoldersOpen: false
  });
  var bounds = new google.maps.LatLngBounds();
  //parser.parse(kmlfile);
  map.fitBounds(bounds);
  var listenx = google.maps.event.addListener(map, "idle", function() { 
    if (map.getZoom() > 14) map.setZoom(14); 
    google.maps.event.removeListener(listenx); 
  });
}

function afterParse() {
        $('select[multiple]').multiselect();
}        

function filterSidebar(searchText){
	parser.filterMap(searchText);    
} 

function filter(){
	document.forms[0].search.value='';
	parser.filterMap();
}   

function filterForFolder(){
	parser.filterByFolder(map);    
} 

/*
 * К всплывающим окнам про юзеров
 */
function PoLoad(el) {
    var $popoverel = el.attr('aria-describedby');  
    if ($popoverel != null) {
      $('#' + $popoverel).popover('destroy');
    }
    else {
      // add initial popovers with LOADING text
      el.popover({
        content: "loading…",
        html: true,
        placement: "auto",
        trigger: "manual"
      });
      el.popover('show');
      $.get(el.data('poload'), function (d) {
        // set new content to popover
        el.data('bs.popover').options.content = d;
        // reshow popover with new content
        el.popover('show');
      });
    }  
}
function PopoverClose(el) {
  var $popover = el.closest('.popover');
  $popover.popover('destroy');
}
  
  function ScrollD(divid) {
    $('html,body').animate({
        scrollTop: $(divid).offset().top
    }, 700);
  }  

/**
 * К маршрутам 
 */
function EdObj(num) {
 var f=document.strip;
 var MyAnc="ed_obj.php?num=" + num + "&trip=" + f.trip.value;
 MyWin=window.open(MyAnc, 'Изменить','scrollbars=yes,resizable=yes,width=950,height=650');
 MyWin.focus();
 return true;
}

function AddObj(num) {
 var f=document.strip;
 var MyAnc="add_obj.php?num=" + num + "&oldtrip=" + f.trip.value;
 MyWin=window.open(MyAnc, 'Добавить','scrollbars=yes,resizable=yes,width=950,height=650');
 MyWin.focus();
 return true;
}

function AddT(num) {
 var f=document.strip;
 var MyAnc="add_t.php?oldtrip=" + f.trip.value;
 MyWin=window.open(MyAnc, 'Добавить','scrollbars=yes,resizable=yes,width=950,height=650');
 MyWin.focus();
 return true;
}

function RmObj(num) {
 var f=document.strip;
 f.num.value = num;
 f.add.value = "Delete";
 f.action = 'ch_trip.php#' + num;
 f.submit();
 return true;
}

function RmObjAll(num) {
 var f=document.strip;
 f.num.value = num;
 f.add.value = "DeleteAll";
 f.action = 'ch_trip.php#' + num;
 f.submit();
 return true;
}

function RmTrip(num) {
 var f=document.strip;
 f.num.value = num;
 f.add.value = "DeleteTrip";
 f.action = 'ch_trip.php#' + num;
 f.submit();
 return true;
}

function AddATrip(num) {
 var v=document.strip;
 var trip=v.trip.value;
 var f=window.opener.document.strip;
 f.num.value = num;
 f.add.value = "AddATrip";
 f.type.value = trip;
 f.action = 'ch_trip.php#' + num;
 f.submit();
 window.close();
 return true;
}

function ButObj(num) {
 var v=document.strip;
 var f=window.opener.document.strip;
 var add = "";
 if (!(v.comment.checked)) {
  add = add + "0";
 }
 if (v.foto.checked) {
  add = add + "z";
 }
 if (v.otklik.checked) {
  add = add + "y";
 }
 if (v.type1.checked) {
  add = add + "1";
 }
 if (v.type2.checked) {
  add = add + "2";
 }
 if (v.type3.checked) {
  add = add + "3";
 }
 if (v.type4.checked) {
  add = add + "4";
 }
 if (v.type5.checked) {
  add = add + "5";
 }
 if (v.type6.checked) {
  add = add + "6";
 }
 if (v.type7.checked) {
  add = add + "7";
 }
 if (v.type8.checked) {
  add = add + "8";
 }
 f.num.value = num;
 f.add.value = add;
 f.action = 'ch_trip.php#' + num;
 f.submit();
 window.close();
 return true;
}

function AddMany(sign) {
 var f=document.strip;
 var v=document.chform;
 var nums="";
 for (var i=0; i <v.elements.length; i++) {
  if (v.elements[i].checked) {
   if (nums != "") {
    nums = nums + "," + v.elements[i].value;
   }
   else {
    nums = v.elements[i].value;
   }
  }
 }
 f.add.value = sign;
 f.num.value = nums;
 f.action = 'ch_trip.php#last';
 f.submit();
 return true;
}
