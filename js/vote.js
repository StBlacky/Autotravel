function CheckF() {
 if (vote.vote0[7].checked) {
  if (vote.vote1[2].checked) {
    alert("Оба балла не могут быть = 0");
    return false;
  }
  b = confirm("Вы уверены в оценке ВПЕЧАТЛЕНИЕ = 0?");
  if (!b) {
    return false;
  }
 }
 if (vote.vote1[2].checked) {
  b = confirm("Вы уверены в оценке ТЕМАТИКА = 0?");
  if (!b) {
    return false;
  }
 }
 return true;
}


