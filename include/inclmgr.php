<?php
include_once('include/func.php');
include_once('include/head00.php');

if ($forum_user_id == 0) {

 include("include/header.php");
 include("include/userset.php");
 echo "<h3 class=travell5c>Авторизация неуспешна.</h3>\n";
 echo "<p class=travell0>Для дальнейшей работы с этой областью сайта необходима авторизация - ввод имени пользователя, зарегистрированного на форуме, и подтверждающего пароля.</p>\n";
 echo "<p class=travell0>Предупреждение. На этой странице не содержится никакой информации, которой бы не было на других страницах сайта. Данный сервис просто помогает участникам форума размещать здесь свою информацию.</p>\n";
// надо бы подумать: на каких страницах не нужно отображать форму входа
//     if () {
 echo "<form name='_forum_enter' method=post action='".$_SERVER["PHP_SELF"]."'>\n";
//     reset ($HTTP_POST_VARS);
//     while (list ($ttkey, $ttval) = each ($HTTP_POST_VARS)) {
//      if (($ttkey <> 'tforumname') && ($ttkey <> 'tforumpassword')) {
//       echo "<input name='$ttkey' value='$ttval' type=hidden>\n";
//      }
//     }
// reset ($_GET);
// while (list ($ttkey, $ttval) = each ($_GET)) {
//    тут ещё надо бы обработать массивы
//    echo "<input name='$ttkey' value='$ttval' type=hidden>\n";
// }
 echo "<table border=0 width=120><tbody class=travell0>\n";
 echo "<tr><td align=right>Имя:</td>\n";
 echo "<td><input name=tforumname value='$tforumname' type=text size=10></td></tr>\n";
 echo "<tr><td align=right>Пароль:</td>\n";
 echo "<td><input name=tforumpassword value='$tforumpassword' type=password size=10></td></tr>\n";
?>
<tr><td align=right>Запомнить имя:</td>
<td>
<select size=1 name="tforumtime">
<option value="0" >На час
<option value="1" >На день
<option value="7" >На неделю
<option value="30">На месяц
<option value="366">На год
<option value="3660">Навсегда
</select>
</td></tr>
<tr><td colspan=2 align=center><input type=submit value='Войти'></td></tr>
</tbody></table></form>

<?php
//     }

 exit;
}

// тут пользователь уже авторизован и его имя лежит в forum_user_name,
// forum_user_level и forum_user_rank тоже показывают куда надо 


?>
