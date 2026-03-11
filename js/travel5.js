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

/* ======= AT MAP FILTER BEGIN (initmapv3 + filter) ======= */
function initmapv3(kmlfile,parent,lat,lng) {
  // lat/lng могут быть не заданы — тогда центр временный, дальше подгоняем по bounds KML
  if (typeof lat === 'undefined' || lat === null) lat = 0;
  if (typeof lng === 'undefined' || lng === null) lng = 0;

  var options = {
    center: new google.maps.LatLng(lat,lng),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  var mapdiv = document.getElementById("gmapv3");
  if (!mapdiv) return;

  if (parent != null) {
    var c=parent.getElementsByTagName('img')[0];
  }

  if (mapdiv.style.display == 'none') {
    mapdiv.style.display = 'block';
    if (mapdiv.style.visibility != 'visible') {
      mapdiv.style.visibility = 'visible';

      // карта + парсер делаем доступными снаружи (для фильтра)
      var map = new google.maps.Map(mapdiv, options);
      window.atGMap = map;

 var parser = new geoXML3.parser({
  map: map,
  processStyles: true,
  singleInfoWindow: true,
  afterParse: function(docs) {
      
      console.log("DOCS LEN:", (parser.docs ? parser.docs.length : 0));
console.log("DOC0 KEYS:", (parser.docs && parser.docs[0] ? Object.keys(parser.docs[0]) : null));
console.log("DOC0 markers/gmarkers:", 
  parser.docs && parser.docs[0] ? 
  ((parser.docs[0].markers ? parser.docs[0].markers.length : 0) + " / " + (parser.docs[0].gmarkers ? parser.docs[0].gmarkers.length : 0)) 
  : "no doc0"
);


    console.log("AFTER PARSE FIRED");
    console.log("docs:", docs);
    console.log("parser.docs:", parser.docs);

    try {
      atMapBuildFilters(map, parser);
    } catch(e) {
      console.error("atMapBuildFilters error:", e);
    }
  }
});

 
      window.atKmlParser = parser;

      parser.parse(kmlfile);

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
  // Если вызов программный (parent == null) — не прячем карту, просто выходим.
  if (parent == null) return;

  mapdiv.style.display = 'none';
  c.setAttribute('src','/jpg/plus.gif');
  c.setAttribute('alt','>');
}

}

/**
 * Карта области: фильтр по типам (ETYPE) и группам (GRP_SLUG)
 * Требует, чтобы kml.php отдавал ExtendedData: ETYPE, GRP_SLUG, GRP_NAME
 */
function atMapBuildFilters(map, parser) {
    console.log("PARSER DOCS:", parser.docs);


  if (!window.atMapFilterEnabled && window.atHideMapFilter) return;

  var typesBox = document.getElementById('at-map-types');
  if (!typesBox) return;

  var markers = [];

  // geoXML3 может класть маркеры в разные поля
  if (parser && parser.docs) {
    parser.docs.forEach(function(doc){
      if (doc.markers) markers = markers.concat(doc.markers);
      if (doc.gmarkers) markers = markers.concat(doc.gmarkers);
    });
  }
console.log("MARKERS FOUND:", markers.length);

  if (!markers.length) return;

  // собираем по типам
  var byType = {};
  markers.forEach(function(m){

    var et = null;

    try {
      if (m.extendedData && m.extendedData.ETYPE)
        et = parseInt(m.extendedData.ETYPE.value || m.extendedData.ETYPE, 10);
      else if (m.kmlData && m.kmlData.extendedData && m.kmlData.extendedData.ETYPE)
        et = parseInt(m.kmlData.extendedData.ETYPE.value, 10);
    } catch(e){}

    if (!et) return;

    // разрешённые типы
    if (window.atMapAllowedTypes &&
        window.atMapAllowedTypes.indexOf(et) === -1) return;

    if (!byType[et]) byType[et] = [];
    byType[et].push(m);
  });

  var types = Object.keys(byType).map(function(x){ return parseInt(x,10); });
  if (!types.length) return;

  // строим чекбоксы
  typesBox.innerHTML = types.map(function(et){

    var label = (window.atMapTypeLabels && window.atMapTypeLabels[et])
                ? window.atMapTypeLabels[et]
                : ("Тип " + et);

    return "<label style='margin-right:14px;display:inline-block;'>" +
           "<input type='checkbox' data-etype='"+et+"' checked> " +
           label +
           "</label>";

  }).join("");

  // обработчик изменения
  typesBox.querySelectorAll("input[type=checkbox]").forEach(function(cb){

    cb.addEventListener('change', function(){

      var et = parseInt(this.getAttribute('data-etype'),10);
      var visible = this.checked;

      if (!byType[et]) return;

      byType[et].forEach(function(marker){
        marker.setVisible(visible);
      });

    });

  });

  // кнопки показать/скрыть
  var btnAll = document.getElementById('at-map-showall');
  var btnNone = document.getElementById('at-map-hideall');

  if (btnAll) {
    btnAll.onclick = function(){
      typesBox.querySelectorAll("input[type=checkbox]").forEach(function(cb){
        cb.checked = true;
        cb.dispatchEvent(new Event('change'));
      });
    };
  }

  if (btnNone) {
    btnNone.onclick = function(){
      typesBox.querySelectorAll("input[type=checkbox]").forEach(function(cb){
        cb.checked = false;
        cb.dispatchEvent(new Event('change'));
      });
    };
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

