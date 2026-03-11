<?php
?>
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 <script src="https://secure.paygateway.ru/widget/payframe/distr/widget/paygateway.js"></script>
 
<script>
function widget_card2card() {
  var widget = new Pgw.Main({operationType: 'CARD2CARD'}),

  paymentOpts = {
    account: ACC010439,
    ip: "<?php echo $_SERVER['REMOTE_ADDR'];?>"
  },
  successFn = function(options) {},
  failureFn = function(reason, options) {};

 widget.c2ctransfer(paymentOpts, successFn, failureFn);
}
</script>

 <widget name='site_widget_table' width='545'> 
<h1>Перевод<br/>с карты<span class="orange"><br>на карту</span></h1> 
Переводите денежные средства с карты на карту быстро и удобно! Заполните все необходимые поля на странице и подтвердите перевод. Срок зачисления денежных средств зависит от банка-эмитента, выпустившего карту получателя и может составлять от нескольких минут до нескольких дней. 
 

<h2>Подробная информация об условиях card2card переводов: </h2>
<li>Максимальная сумма одного перевода без учета комиссии - 150 000 рублей;</li> 
<li>Максимальная сумма переводов по одной карте без учета комиссий за один календарный месяц - 600 000 рублей;</li> 
<li>Ограничение по количеству переводов с использованием одной карты - не более 4 операций в день и не более 15 операций за 4 календарных дня подряд;</li> 
<li>Переводы осуществляются только в рублях РФ и только между картами банков, зарегистрированных в РФ;</li> 
<li>Срок зачисления денежных средств зависит от банка, выпустившего карту получателя, и может составлять от нескольких минут до нескольких дней;</li> 
<li>Операция списания по карте отправителя проводится исключительно по технологии 3DSecure (Verified by Visa/MasterCard SecureCode);</li> 
<li>Банки-эмитенты карт могут устанавливать дополнительные условия и ограничения для проведения переводов с карты на карту.</li> 

</widget>
<widget name='site_widget_normalText'> 
<hr/> 
<div class="thin-text" style="text-align:center;font-size:30px;margin:30px 0;">Остались вопросы? 
<a href='/site/contacts' class="thin-text">Свяжитесь с нами</a> и мы будем рады вам помочь. 
</div> 
</widget> 

<button OnClick="widget_card2card()">Перейти</button>