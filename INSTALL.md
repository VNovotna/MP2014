System requirements
-------------------
* Unix like operating system
* git (or you can download the source manualy)
* curl
* Web server cabable of running php >=5.4

Instructions
------------
* $ git clone git@bitbucket.org:vikyN/mc-server-wrapper.git
* $ cd mc-server-wrapper
* $ curl -sS https://getcomposer.org/installer | php
* $ php composer.phar install
* then point your web server to www/ folder of this project. '# ln -s /path/to/mc-server-wrapper/www/ /var/www/mcsw' should be enough