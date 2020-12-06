# Dreamchaser
Dreamchaser is a web application that can help you manage your home budget. I introduced all the features in the video on my personal website: https://www.bartoszchodyla.pl/portoflio/aplikacja-webowa-dreamchaser. If you want to test the application, it's available at the following address: https://dreamchaser.bartoszchodyla.pl/

<h2>Installation</h2>
If you want to run the app on your computer to play with the code, those are the steps you need to follow: <br/>
1. Create a local server for PHP and MySQL. In order to do that you have to install one of the web server solution stack packages e.g.: XAMPP or AMPPS (both are free). <br/>
2. Run phpMyAdmin, create a database and import the database I developed (/dreamchaser.sql file).<br/>
3. Open a config file (/App/Config.php) and configure it - enter your database data and reCAPTCHA site and secret key. To generate the keys visit: https://www.google.com/recaptcha/about<br/>
4. Put all app files in the web server solution stack package's folder (a specific folder depends on the stack package. Read its documentation to find out more).<br/>
5. Run the project in the browser (again, read the stack package documentation to do that in a proper way).

