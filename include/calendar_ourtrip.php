<?php
// ===============================
//  НОВЫЙ КАЛЕНДАРЬ ДЛЯ OURTRIP
// ===============================
//  - Понедельник — первый день недели
//  - Русские месяцы
//  - Подсветка дней, где есть поездки
//  - Ссылка на список поездок за дату (ddd=YYYY-MM-DD)
//  - Минимум зависимостей
// ===============================

setlocale(LC_TIME, 'ru_RU.UTF-8');

// параметры месяца
$month = isset($_GET['cm']) ? (int)$_GET['cm'] : (int)date('m');
$year  = isset($_GET['cy']) ? (int)$_GET['cy'] : (int)date('Y');

$firstDayTimestamp = mktime(0,0,0, $month, 1, $year);
$daysInMonth = (int)date('t', $firstDayTimestamp);

// день недели первого числа (1 = понедельник … 7 = воскресенье)
$firstWeekday = (int)date('N', $firstDayTimestamp); 

// название месяца
$monthName = strftime('%B', $firstDayTimestamp); // “Декабрь”

// предыдущий / следующий месяц
$prevTs = strtotime("-1 month", $firstDayTimestamp);
$nextTs = strtotime("+1 month", $firstDayTimestamp);

$prevLink = "cy=" . date("Y", $prevTs) . "&cm=" . date("m", $prevTs);
$nextLink = "cy=" . date("Y", $nextTs) . "&cm=" . date("m", $nextTs);

// --- выбираем поездки за месяц ---
$firstDay = date("Y-m-01", $firstDayTimestamp);
$lastDay  = date("Y-m-t",  $firstDayTimestamp);

$sql = "
    SELECT id, datebeg, dateend
    FROM ourtrip
    WHERE
        datebeg <= '$lastDay'
        AND
        (dateend IS NULL OR dateend >= '$firstDay')
";

$res = $dbi->query($sql);

// массив дней → есть ли поездки
$tripDays = [];

while ($row = $res->fetch_assoc()) {
    $b = strtotime($row['datebeg']);
    $e = strtotime($row['dateend'] ?: $row['datebeg']);

    for ($d = 1; $d <= $daysInMonth; $d++) {
        $ts = strtotime("$year-$month-$d");
        if ($ts >= $b && $ts <= $e) {
            $tripDays[$d] = true;
        }
    }
}
?>

<div class="trip-calendar">
    <div class="cal-header">
        <a class="cal-arrow" href="?<?php echo $prevLink; ?>">‹</a>
        <span class="cal-title"><?php echo ucfirst($monthName)." $year"; ?></span>
        <a class="cal-arrow" href="?<?php echo $nextLink; ?>">›</a>
    </div>

    <table class="cal-table">
        <tr>
            <th>Пн</th><th>Вт</th><th>Ср</th><th>Чт</th><th>Пт</th><th>Сб</th><th>Вс</th>
        </tr>

        <tr>
        <?php
        // пустые клетки перед первым днём
        for ($i=1; $i<$firstWeekday; $i++) {
            echo "<td class='cal-empty'></td>";
        }

        $weekday = $firstWeekday;

        for ($day = 1; $day <= $daysInMonth; $day++, $weekday++) {

            $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $hasTrip = isset($tripDays[$day]);

            $class = "cal-day";
            if ($hasTrip) $class .= " cal-trip";
            if ($dateStr == date("Y-m-d")) $class .= " cal-today";

            echo "<td class='$class'>";

            if ($hasTrip) {
                echo "<a href='/ourtrip.php?ddd=$dateStr'>$day</a>";
            } else {
                echo $day;
            }

            echo "</td>";

            if ($weekday % 7 == 0) echo "</tr><tr>";
        }

        // закрываем последние пустые клетки
        while ($weekday % 7 != 1) {
            echo "<td class='cal-empty'></td>";
            $weekday++;
        }
        ?>
        </tr>
    </table>
</div>
<style>
    .trip-calendar {
    background: var(--color-bg-block);
    border: 1px solid var(--color-border);
    padding: 10px 12px;
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0,0,0,.08);
    margin-bottom: 20px;
}

.cal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.cal-title {
    font-weight: 600;
    font-size: 1.05em;
    color: var(--color-accent);
}

.cal-arrow {
    font-size: 1.2em;
    color: var(--color-accent);
    text-decoration: none;
}

.cal-arrow:hover {
    color: var(--color-accent-dark);
}

.cal-table {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
}

.cal-table th {
    padding: 4px 0;
    font-size: 0.9em;
    color: var(--color-text-light);
}

.cal-table td {
    width: 14.2%;
    height: 28px;
    padding: 2px;
    font-size: 0.9em;
}

.cal-day a {
    display: block;
    width: 100%;
    height: 100%;
    text-decoration: none;
    color: var(--color-text-main);
}

.cal-day a:hover {
    color: var(--color-accent-dark);
    text-decoration: underline;
}

.cal-empty {
    background: transparent;
}

.cal-trip {
    background: #e6eee6;
    border-radius: 4px;
}

.cal-today {
    outline: 2px solid var(--color-accent);
    border-radius: 4px;
}

</style>
