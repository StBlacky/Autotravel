<?php
include_once('include/config.php');
include_once('include/func.php');
$bootstrap = 1;
include_once('include/head00.php');

$title = "Маршруты для самостоятельных путешествий по России";
$metatags = "готовые маршруты автопутешествий, маршруты выходного дня, маршруты для отпуска";
$metadesc = "Готовые маршруты по России для самостоятельных автопутешествий: по длительности и по интересам, а также маршрутизатор для планирования поездки.";

include('include/header_bs.php');
?>

<div class="container-fluid">

  <h1 class="neucha">Маршруты</h1>

  <p class="travell0">
    От <strong>готовых маршрутов самостоятельных поездок</strong> для начинающих путешественников
    до самостоятельного планирования при помощи маршрутизатора.
  </p>

  <!-- Вводный смысловой блок -->
  <div class="note-block">
    <p>
      Этот раздел мы разработали специально для начинающих автопутешественников.
      Порой от увлекательного приключения нас останавливает только незнание — с чего начать?
      Тогда на помощь приходит опыт предшественников, который мы постарались отразить
      в предложенных маршрутах.
    </p>
    <p>
      Предлагаемые маршруты не являются догмой, однако каждый из них может быть принят
      как руководство к действию: с планом поездки, рекомендациями по ночлегу,
      питанию и списком достопримечательностей.
    </p>
    <p class="mb-0">
      Рекомендации по питанию и ночлегу подбираются автоматически
      в соответствии с рейтингом гостиниц и кафе, представленных на сайте.
    </p>
  </div>

  <!-- Два столбца навигации -->
  <div class="row">

    <!-- По длительности -->
    <div class="col-md-6 col-xs-12">
      <h2 class="neucha">По длительности поездки</h2>

      <?php
      $sql_d = "
        SELECT DISTINCT trip_desc.d AS d, trip_d.descrip AS descrip
        FROM trip_desc
        JOIN trip_d ON trip_d.id = trip_desc.d
        ORDER BY trip_d.days
      ";
      $res_d = $dbi->query($sql_d);
      if ($res_d) {
        while ($row = mysqli_fetch_array($res_d)) {
          echo '<p class="travell0"><a href="/triptrackm.php/d'.$row['d'].'">'.$row['descrip'].'</a></p>';
        }
        mysqli_free_result($res_d);
      }
      ?>

      <p class="travell0">
        Для удобства все маршруты сгруппированы по длительности:
        маршруты выходного дня, праздничные маршруты и маршруты для отпуска.
      </p>
    </div>

    <!-- По интересам -->
    <div class="col-md-6 col-xs-12">
      <h2 class="neucha">По интересам</h2>

      <p class="travell0">
        Мы постарались представить как сбалансированные маршруты с разнообразными
        достопримечательностями, так и маршруты конкретной направленности.
      </p>

      <?php
      $sql_ex = "
        SELECT DISTINCT trip_desc.ex AS ex, trip_ex.descrip AS descrip
        FROM trip_desc
        JOIN trip_ex ON trip_ex.id = trip_desc.ex
        ORDER BY trip_ex.descrip
      ";
      $res_ex = $dbi->query($sql_ex);
      if ($res_ex) {
        while ($row = mysqli_fetch_array($res_ex)) {
          echo '<p class="travell0"><a href="/triptrackm.php/e'.$row['ex'].'">'.$row['descrip'].'</a></p>';
        }
        mysqli_free_result($res_ex);
      }
      ?>

      <p class="travell0">
        Особое внимание уделено отдыху с детьми —
        маршрутам с интерактивными достопримечательностями
        и спокойным графиком.
      </p>
    </div>

  </div>

  <!-- Известные маршруты -->
  <h2 class="neucha">Известные туристические маршруты</h2>
  <p class="travell0">
    <a href="/brends.php">Золотое кольцо России</a>
  </p>

  <!-- Маршрутизатор -->
  <h2 class="neucha" id="marshrutizator">Маршрутизатор</h2>

  <div class="note-block">
    <p>
      Если вы не хотите ограничивать себя рамками готовых маршрутов,
      пора обращаться к маршрутизатору.
    </p>
    <p>
      Наш маршрутизатор не предназначен для поиска кратчайшего расстояния.
      Он помогает:
    </p>
    <ul>
      <li>наметить маршрут;</li>
      <li>посмотреть, что интересного есть по пути;</li>
      <li>спланировать остановки и ответвления;</li>
      <li>подготовить посещение достопримечательностей;</li>
      <li>распечатать маршрут или поделиться им.</li>
    </ul>
    <p class="mb-0">
      Перейти к маршрутизатору и <a href="/ch_trip.php"><strong>проложить маршрут</strong></a>.
    </p>
  </div>

</div>

<?php include('include/footer_bs.php'); ?>
