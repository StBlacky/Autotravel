</div> <!--  конец вывода основной части -->
<?php
if (!($_short_footer)) { // реклама и соцсети
?>
<div class="footerline">
    <!--LiveInternet-->
    <!--noindex-->
    <div class="row">
    <div class="col-xs-6">
      <script language="JavaScript" type="text/javascript">
      document.write('<a href="//www.liveinternet.ru/click" '+
        'target=liveinternet><img src="//counter.yadro.ru/hit?t43.2;r'+
        escape(top.document.referrer)+((typeof(screen)=='undefined')?'':
        ';s'+screen.width+'*'+screen.height+'*'+(screen.colorDepth?
        screen.colorDepth:screen.pixelDepth))+';'+Math.random()+
        '" border=0 width=31 height=31 alt="liveinternet.ru"></a>');
    </script>
    <!--/noindex-->
    <!--/LiveInternet-->
    </div>
    <div class="col-xs-6"><?php echo $reclama11;?></div></div>
</div> <!-- footerline  -->
<?php 
}
include_once('footer_bs_javascript.php');
?>    

</div> <!-- //maincontainer -->
</body>
</html>

