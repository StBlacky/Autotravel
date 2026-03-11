
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
