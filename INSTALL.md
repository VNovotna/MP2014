System requirements
-------------------
* Unix like operating system
* git (or you can download the source manualy)
* curl and curl php extension
* Web server cabable of running php >=7.1
* GNU screen
* java (for Minecraft)

Instructions
------------
* $ git clone https://vikyN@bitbucket.org/vikyN/mc-server-wrapper.git
* $ cd mc-server-wrapper
* $ curl -sS https://getcomposer.org/installer | php
* $ php composer.phar install
* then point your web server to *www/* folder of this project. eg. '# ln -s /path/to/mc-server-wrapper/www/ /var/www/mcsw'
* make sure that *rwa/* folder is writable to web server. eg. '# chown user:www-data rwa/ -R && chmod 771 rwa/ -R'
* log in to aplication like user *admin* with password *admin1*
* head into Admin -> Settings and check it, storage is the most important
