Shared Host Installation
========================

If you do not have SSH access to your server, fear not! You can still run
composer and download the SDK. Here's how...

Installation
------------

Linux / Mac OSX:  
*PHP is typically installed by default, consult your distribution documentation. Instructions from [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).*  

1. curl -sS https://getcomposer.org/installer | php  
2. php composer.phar require mailgun/mailgun-php:~1.7.1  
3. The files will be downloaded to your local computer.   
4. Upload the files to your webserver.   


Windows:  
*PHP must be installed on your computer, [download](http://windows.php.net/download/0). Instructions from [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-windows).* 

1. Download and run [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe).  
2. Open a Command Prompt and type "php composer require mailgun/mailgun-php:~1.7.1".  
3. The files will be downloaded to your local computer.   
4. Upload the files to your webserver.   


Support and Feedback
--------------------

Be sure to visit the Mailgun official 
[documentation website](http://documentation.mailgun.com/) for additional 
information about our API. 

If you find a bug, please submit the issue in Github directly. 
[Mailgun-PHP Issues](https://github.com/mailgun/Mailgun-PHP/issues)

As always, if you need additional assistance, drop us a note at 
[support@mailgun.com](mailto:support@mailgun.com).
