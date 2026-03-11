function roll(item,value) {
 item.innerHTML=value;
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
 var MyAnc="/foto/" + alb + "/" + foto + ".html";
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
   mapdiv.style.display = 'none';
   if (parent != null) {
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
   mapdiv.style.display = 'none';
   if (parent != null) {
    c.setAttribute('src','/jpg/plus.gif');
    c.setAttribute('alt','>');
   }
  }
}
function atDbg(msg) {
  var el = document.getElementById('at-map-types');
  if (!el) return;
  el.innerHTML = "<div style='margin:6px 0; padding:6px; border:1px dashed #999; background:#fff;'>"
    + msg + "</div>" + el.innerHTML;
}

function initmapv3(kmlfile,parent,lat,lng) {
    atDbg("initmapv3 вызван, kml=" + kmlfile);

    console.log("INITMAPV3 CALLED", kmlfile);

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
   mapdiv.style.display = 'none';
   if (parent != null) {
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
      draggingCursor: 'move'
  };
  mapdiv.style.visibility = 'visible';
  mapdiv.style.display = 'block';
  map = new google.maps.Map(mapdiv, options);
  parser = new KmlMapParser({ map: map,
      afterParseFn: afterParse,
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

function afterParse() {
    atDbg("afterParse сработал, docs=" + (docs ? docs.length : 0));

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

