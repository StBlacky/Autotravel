/* =====================================================
   AT MAP UNIFIED MARKERS v1
   ===================================================== */

window.atMapShowGroupColors = false;

/* =============================
   LOAD AREA JSON
   ============================= */

function atLoadAreaJson(url, callback) {

  fetch(url)
    .then(function(r){
      if (!r.ok) {
        console.log("JSON load error", r.status);
        callback([]);
        return null;
      }
      return r.json();
    })
    .then(function(data){
      if (!data || !data.items) {
        callback([]);
        return;
      }
      callback(data.items);
    })
    .catch(function(err){
      console.log("Fetch failed", err);
      callback([]);
    });
}

function atBuildPopupHtml(item) {

  var html = "";

  if (item.name) {
    html += "<div style='font-weight:bold; margin-bottom:6px;'>";
    html += item.name;
    html += "</div>";
  }

  if (item.description) {
    html += "<div style='margin-bottom:8px;'>";
    html += item.description;
    html += "</div>";
  }

  if (item.url) {
    html += "<div style='margin-top:6px;'>";
    html += "<a href='" + item.url + "' target='_blank' style='font-weight:600;'>";
    html += "Подробности, фото, отзывы посетителей →";
    html += "</a>";
    html += "</div>";
  }

  return html;
}

/* ---------- Создание HTML маркера ---------- */

function atBuildMarkerHtml(item, iconUrl) {

  var div = document.createElement("div");
  div.className = "at-marker";

  if (item.etype !== undefined && item.etype !== null) {
    div.classList.add("at-type-" + item.etype);
  }

  var img = document.createElement("img");
  img.src = iconUrl;

  div.appendChild(img);

  return div;
}


/* ---------- Определение иконки ---------- */
function atGetGroupColor(slug) {

  var map = {
      "architecture": "#e53935",
    "children": "#1e88e5",
    "nature": "#43a047",
    "tech": "#e50935",
    "sculpture": "#1e00e5",
    "ruins": "#431147",
    "museums": "#8e24aa"
  };

  return map[slug] || null;
}

function atGetIconUrl(item) {

  var color = "#01633c";

  if (item.etype == 2) color = "#20258c";
  if (item.etype == 3) color = "#6a1b9a";
  if (item.etype == 5) color = "#ef6c00";
  if (item.etype == 6) color = "#8f0d0d";
  if (item.etype == 8) color = "#455a64";
  if (item.etype == 0) color = "#616161";

  var symbol = "";

  if (window.atMapShowGroupColors && item.grpSlug) {

    var groupColor = atGetGroupColor(item.grpSlug);

    if (groupColor) {
      symbol = `<circle cx="12" cy="11" r="4"
                 fill="${groupColor}"
                 stroke="#ffffff"
                 stroke-width="1"/>`;
    }
  }

  var svg = `
<svg width="22" height="36" viewBox="0 0 24 36" xmlns="http://www.w3.org/2000/svg">

  <path
    fill="none"
    stroke="#ffffff"
    stroke-width="2"
    stroke-linejoin="round"
    stroke-linecap="round"
    d="M12 3 C8.5 3 5 6 5 10 C5 14.5 9.5 17.5 11 20 C11.6 21.2 11.8 23 12 29 C12.2 23 12.4 21.2 13 20 C14.5 17.5 19 14.5 19 10 C19 6 15.5 3 12 3 Z"/>

  <path
    fill="${color}"
    d="M12 3 C8.5 3 5 6 5 10 C5 14.5 9.5 17.5 11 20 C11.6 21.2 11.8 23 12 29 C12.2 23 12.4 21.2 13 20 C14.5 17.5 19 14.5 19 10 C19 6 15.5 3 12 3 Z"/>

  ${symbol}

</svg>`;

  return "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(svg);
}


/* ---------- Универсальное создание маркера ---------- */

function atCreateMarker(engine, map, item) {

  var iconUrl = atGetIconUrl(item);

  /* ===== GOOGLE ===== */
  if (engine === "google") {

    var html = atBuildMarkerHtml(item, iconUrl);

    var marker = new google.maps.marker.AdvancedMarkerElement({
      map: map,
      position: { lat: parseFloat(item.lat), lng: parseFloat(item.lng) },
      content: html
    });

    marker.atMapRef  = map;
    marker.atEtype   = item.etype;
    marker.atGrpSlug = item.grpSlug;
    marker.atGrpName = item.grpName;

    if (!window.atGoogleInfoWindow) {
      window.atGoogleInfoWindow = new google.maps.InfoWindow();
    }

    marker.addListener("click", function(){

      window.atGoogleInfoWindow.setContent(
        "<div style='max-width:260px'>" +
          atBuildPopupHtml(item) +
        "</div>"
      );

      window.atGoogleInfoWindow.open({
        map: map,
        anchor: marker
      });
    });

    return marker; // ✅ ВАЖНО
  }

  /* ===== LEAFLET ===== */
  if (engine === "leaflet") {

    var icon = L.icon({
      iconUrl: iconUrl,
      iconSize: [24, 36],
      iconAnchor: [12, 32],
      popupAnchor: [0, -30]
    });

    var marker = L.marker(
      [parseFloat(item.lat), parseFloat(item.lng)],
      { icon: icon }
    );

    marker.bindPopup(atBuildPopupHtml(item));

    marker.atMapRef  = map;
    marker.atEtype   = item.etype;
    marker.atGrpSlug = item.grpSlug;
    marker.atGrpName = item.grpName;

    marker.addTo(map);

    return marker; // ✅ уже было, но оставляем
  }

  return null;
}

/* ---------- Построение всех маркеров ---------- */

function atBuildMarkers(options) {

  /*
    options = {
      engine: 'google' | 'leaflet',
      map: map,
      items: items
    }
  */

  var markers = [];

  options.items.forEach(function(item){

    var marker = atCreateMarker(
      options.engine,
      options.map,
      item
    );

    if (marker) {
      markers.push(marker);
    }

  });

  return markers;
}

function atUpdateMarkerIcons(markers, engine) {

  markers.forEach(function(marker){

    var item = {
      etype: marker.atEtype,
      grpSlug: marker.atGrpSlug
    };

    var iconUrl = atGetIconUrl(item);

    if (engine === "leaflet") {

      var icon = L.icon({
        iconUrl: iconUrl,
        iconSize: [24, 36],
        iconAnchor: [12, 32],
        popupAnchor: [0, -30]
      });

      marker.setIcon(icon);
    }

    if (engine === "google") {

      var html = atBuildMarkerHtml(item, iconUrl);
      marker.content = html;
    }

  });
}

function atUpdateGroupCheckboxColors() {

  var boxGroups = document.getElementById('at-map-groups');
  if (!boxGroups) return;

  boxGroups.querySelectorAll("input").forEach(function(input){

    var slug = input.dataset.grp;
    var label = input.parentNode;

    var color = atGetGroupColor(slug);

    if (window.atMapShowGroupColors && color) {
      label.style.color = color;
      label.style.fontWeight = "600";
    } else {
      label.style.color = "";
      label.style.fontWeight = "";
    }
  });
}

/* =====================================================
   AREA MAP INIT (только для area.php)
   ===================================================== */

function atMapInit() {

  if (window.atMapInitDone) return;
  window.atMapInitDone = true;

  var mapdiv = document.getElementById("gmapv3");
  if (!mapdiv) return;

  /* =============================
     TOGGLE GROUP COLORS
     ============================= */

  var toggle = document.getElementById("at-toggle-group-colors");

  if (toggle && !window.atToggleInit) {

    window.atToggleInit = true;

    toggle.addEventListener("change", function(){

      window.atMapShowGroupColors = this.checked;

      if (window.atCurrentMarkers) {
        atUpdateMarkerIcons(
          window.atCurrentMarkers,
          window.atCurrentEngine
        );
      }

      atUpdateGroupCheckboxColors();
    });
  }

  mapdiv.style.display = 'block';
  mapdiv.style.visibility = 'visible';

  var mode = parseInt(window.atMapMode || 0, 10);
  var jsonUrl = window.atMapJsonUrl;

  if (!jsonUrl) {
    console.log("atMapJsonUrl not set");
    return;
  }

  /* =============================
     LEAFLET (OSM + Yandex)
     ============================= */

  if (mode === 1 || mode === 2) {

    var map = new L.Map('gmapv3', {
      renderer: L.canvas(),
      center: new L.LatLng(58, 40),
      zoom: 6,
      scrollWheelZoom: false
    });

    map.options.maxZoom = 17;

    if (mode === 1) {
      var osm = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
      map.addLayer(osm);
    }

    if (mode === 2) {
      var yndx = new L.Yandex();
      map.addLayer(yndx);
      map.addControl(new L.Control.Layers({
        "Карта": yndx,
        "Спутник": new L.Yandex("hybrid")
      }));
    }

    map.on('focus', function() { map.scrollWheelZoom.enable(); });
    map.on('blur', function() { map.scrollWheelZoom.disable(); });

    atLoadAreaJson(jsonUrl, function(items){

      if (!items || !items.length) return;

      var bounds = [];

      items.forEach(function(item){
        if (item.lat && item.lng) {
          bounds.push([item.lat, item.lng]);
        }
      });

      if (bounds.length > 0) {
        map.fitBounds(bounds);
      }

      var markers = atBuildMarkers({
        engine: "leaflet",
        map: map,
        items: items
      });

      window.atCurrentMarkers = markers;
      window.atCurrentEngine  = "leaflet";
      window.atCurrentMap     = map;
      window.atMapItems       = items;

      atMapBuildFilters(markers, "leaflet", map);
    });


  }

  /* =============================
     GOOGLE
     ============================= */

  if (mode === 3) {

    var map = new google.maps.Map(mapdiv, {
      center: { lat: 58, lng: 40 },
      zoom: 6,
      mapId: "autotravel-map"
    });

    atLoadAreaJson(jsonUrl, function(items){

      if (!items || !items.length) return;

      var bounds = new google.maps.LatLngBounds();

      items.forEach(function(item){
        if (item.lat && item.lng) {
          bounds.extend(new google.maps.LatLng(item.lat, item.lng));
        }
      });

      if (!bounds.isEmpty()) {
        map.fitBounds(bounds);
      }

      var markers = atBuildMarkers({
        engine: "google",
        map: map,
        items: items
      });

      window.atCurrentMarkers = markers;
      window.atCurrentEngine  = "google";
      window.atCurrentMap     = map;
      window.atMapItems       = items;

      atMapBuildFilters(markers, "google", map);
    });


  }
}



/* =====================================================
   FILTERS FOR LEAFLET (AREA ONLY)
   ===================================================== */

function atMapBuildFilters(markers, engine, map) {

  var boxTypes  = document.getElementById('at-map-types');
  var boxGroups = document.getElementById('at-map-groups');
  if (!boxTypes) return;

  var byType = {};
  var byGroup = {};
  var groupToType = {};

  markers.forEach(function(marker){

    if (marker.atEtype != null) {
      if (!byType[marker.atEtype]) byType[marker.atEtype] = [];
      byType[marker.atEtype].push(marker);
    }

    if (marker.atGrpSlug) {

      if (!byGroup[marker.atGrpSlug]) {
        byGroup[marker.atGrpSlug] = {
          name: marker.atGrpName || marker.atGrpSlug,
          markers: []
        };
      }

      byGroup[marker.atGrpSlug].markers.push(marker);

      if (marker.atEtype != null) {
        groupToType[marker.atGrpSlug] = marker.atEtype;
      }
    }
  });

  var allowed = window.atMapAllowedTypes || [];
  var labels  = window.atMapTypeLabels  || {};

  /* ===== TYPES ===== */

  var htmlTypes = "";

  Object.keys(byType)
    .map(function(t){ return parseInt(t, 10); })
    .filter(function(t){
      return allowed.length === 0 || allowed.indexOf(t) !== -1;
    })
    .sort(function(a,b){ return a-b; })
    .forEach(function(typeNum){

      var label = labels[typeNum];
      if (!label) {
        label = (typeNum === 0)
          ? "Объекты без координат"
          : "Тип " + typeNum;
      }

      htmlTypes += "<label style='margin-right:12px'>";
      htmlTypes += "<input type='checkbox' data-etype='"+typeNum+"' checked> ";
      htmlTypes += label;
      htmlTypes += "</label>";
    });

  boxTypes.innerHTML = htmlTypes;

  /* ===== GROUPS ===== */

  if (boxGroups) {

    var htmlGroups = "";

    Object.keys(byGroup).sort().forEach(function(g){

      htmlGroups += "<label style='margin-right:12px'>";
      htmlGroups += "<input type='checkbox' data-grp='"+g+"' checked> ";
      htmlGroups += byGroup[g].name;
      htmlGroups += "</label>";
    });

    boxGroups.innerHTML = htmlGroups;
  }

  var typeInputs  = boxTypes.querySelectorAll("input");
  var groupInputs = boxGroups ? boxGroups.querySelectorAll("input") : [];

  /* ===== VISIBILITY ===== */

  function setVisible(marker, visible) {

    if (engine === "leaflet") {
      if (visible) marker.addTo(map);
      else map.removeLayer(marker);
    }

    if (engine === "google") {
      // AdvancedMarkerElement: marker.map = map|null
      if (marker && ('map' in marker)) {
        marker.map = (visible ? map : null);
      } else if (marker && typeof marker.setMap === 'function') {
        // fallback for classic Marker
        marker.setMap(visible ? map : null);
      }
    }
  }

  function updateVisibility() {

    var activeTypes = {};
    var activeGroups = {};

    typeInputs.forEach(function(input){
      activeTypes[input.dataset.etype] = input.checked;
    });

    groupInputs.forEach(function(input){
      activeGroups[input.dataset.grp] = input.checked;
    });

    markers.forEach(function(marker){

      var typeOk  = marker.atEtype != null
        ? activeTypes[marker.atEtype]
        : true;

      var groupOk = marker.atGrpSlug
        ? activeGroups[marker.atGrpSlug]
        : true;

      setVisible(marker, typeOk && groupOk);
    });
  }

  /* ===== TYPE → GROUP SYNC ===== */

  typeInputs.forEach(function(input){

    input.addEventListener("change", function(){

      var type = input.dataset.etype;
      var checked = input.checked;

      groupInputs.forEach(function(gInput){

        var grp = gInput.dataset.grp;

        if (groupToType[grp] == type) {
          gInput.checked = checked;
        }
      });

      updateVisibility();
    });
  });

  /* ===== GROUP → TYPE SYNC ===== */

  groupInputs.forEach(function(input){

    input.addEventListener("change", function(){

      var grp  = input.dataset.grp;
      var type = groupToType[grp];

      if (type != null) {

        if (input.checked) {

          // включаем тип
          typeInputs.forEach(function(tInput){
            if (tInput.dataset.etype == type) {
              tInput.checked = true;
            }
          });

        } else {

          // если все группы типа выключены — выключаем тип
          var anyChecked = false;

          groupInputs.forEach(function(gInput){
            if (groupToType[gInput.dataset.grp] == type && gInput.checked) {
              anyChecked = true;
            }
          });

          if (!anyChecked) {
            typeInputs.forEach(function(tInput){
              if (tInput.dataset.etype == type) {
                tInput.checked = false;
              }
            });
          }
        }
      }

      updateVisibility();
    });
  });

  atUpdateGroupCheckboxColors();

  // ВАЖНО: применяем allowedTypes сразу (без ожидания клика)
  updateVisibility();
}

function atParseKml(url, callback) {

  fetch(url)
    .then(function(r){ return r.text(); })
    .then(function(text){

      var parser = new DOMParser();
      var xml = parser.parseFromString(text, "text/xml");

      var placemarks = xml.getElementsByTagNameNS("*", "Placemark");
      var items = [];

      for (var i = 0; i < placemarks.length; i++) {

        var pm = placemarks[i];

        var nameEl  = pm.getElementsByTagNameNS("*", "name")[0];
        var descEl  = pm.getElementsByTagNameNS("*", "description")[0];
        var coordEl = pm.getElementsByTagNameNS("*", "coordinates")[0];

        if (!coordEl) continue;

        var coords = coordEl.textContent.trim().split(",");
        var lng = parseFloat(coords[0]);
        var lat = parseFloat(coords[1]);

        var etype   = null;
        var grpSlug = null;
        var grpName = null;
        var grpIcon = null;

        var extended = pm.getElementsByTagNameNS("*", "ExtendedData")[0];

        if (extended) {

          var dataEls = extended.getElementsByTagNameNS("*", "Data");

          for (var j = 0; j < dataEls.length; j++) {

            var dataName = dataEls[j].getAttribute("name");
            var valueNode = dataEls[j].getElementsByTagNameNS("*", "value")[0];
            if (!valueNode) continue;

            var val = valueNode.textContent.trim();

            if (dataName === "ETYPE")     etype   = parseInt(val, 10);
            if (dataName === "GRP_SLUG")  grpSlug = val;
            if (dataName === "GRP_NAME")  grpName = val;
            if (dataName === "GRP_ICON")  grpIcon = val;
          }
        }

        /* ✅ ВАЖНО: если ETYPE отсутствует — считаем это 0 (города) */
        if (etype === null || isNaN(etype)) {
          etype = 0;
        }

        items.push({
          lat: lat,
          lng: lng,
          name: nameEl ? nameEl.textContent : "",
          description: descEl ? descEl.textContent : "",
          etype: etype,
          grpSlug: grpSlug,
          grpName: grpName,
          grpIcon: grpIcon
        });
      }

      callback(items);
    });
}