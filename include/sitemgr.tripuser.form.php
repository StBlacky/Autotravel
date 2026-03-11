<?php
// Форма добавления / редактирования участника поездки
// Показывается, когда $adu == 1

?>
<h3>Участник поездки</h3>

<form name="tripuser" action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="admt" value="tripuser">
    <input type="hidden" name="it" value="<?=$it?>">
    <input type="hidden" name="uid" value="<?=$uid?>">

    <table class="table table-sm table-bordered" style="max-width:650px;">
        <tr>
            <td style="width:180px;" align="right">
                <input type="button"
                       value="Найти участника форума"
                       onclick="SelId('tripuser','tripusername','/f_user.php')">
            </td>
            <td>
                <input type="hidden" name="tripusernameid" value="<?=$tripusernameid?>">
                <input type="text" name="tripusername" size="50" value="<?=$tripusername?>">
            </td>
        </tr>

        <tr>
            <td align="right">Комментарий:</td>
            <td>
                <textarea name="userdescrip" rows="5" cols="60"><?=$tripuserdescrip?></textarea>
            </td>
        </tr>

        <tr>
            <td align="right">Взрослых:</td>
            <td><input type="text" name="cntad" size="5" value="<?=$tripcntad?>"></td>
        </tr>

        <tr>
            <td align="right">Детей:</td>
            <td><input type="text" name="cntch" size="5" value="<?=$tripcntch?>"></td>
        </tr>

        <tr>
            <td align="right">Организатор:</td>
            <td>
                <label>
                    <input type="radio" name="triptypeuser" value="0" <?=($triptypeuser==1?'':'checked')?>>
                    Нет
                </label>
                &nbsp;&nbsp;
                <label>
                    <input type="radio" name="triptypeuser" value="1" <?=($triptypeuser==1?'checked':'')?>>
                    Да
                </label>
            </td>
        </tr>

        <tr>
            <td></td>
            <td>
                <input type="submit" name="submittripuser" value="Сохранить" class="btn btn-primary">
            </td>
        </tr>
    </table>
</form>
