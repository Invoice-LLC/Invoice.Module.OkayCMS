<h1>Invoice Payment Module</h1>

<h3>Установка</h3>

1. Скачайте [архив](https://github.com/Invoice-LLC/Invoice.Module.OkayCMS/archive/master.zip) и разархивируйте в корневую папку сайта
2. Перейдите во вкладку **Настройки сайта->Способы оплаты**, затем нажмите "Добавить способ оплаты"
3. Заполните форму как показано на скриншоте, затем нажмите "Применить"
![Imgur](https://imgur.com/gxLpwlx.png)
4. Добавьте уведомление в личном кабинете Invoice(Вкладка Настройки->Уведомления->Добавить)
с типом **WebHook** и адресом: **%URL сайта%/payment/invoice/callback.php**<br>
![Imgur](https://imgur.com/lMmKhj1.png)
