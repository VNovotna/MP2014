TODO

* GameSettings: new minecraft version (14w10) changes format of config files. The're json now. Intervention needed. [todo, top priority]
* Config file: some values are unused eg number of backups and executable [todo]
* VersionManager: make download() more secure [todo]
* Commands: check if port is free on startup and offer alternative port [todo]
* None: There is no way how to delete server [todo]
* Config and VersionManager: find way how to use more regex variants [todo]
* Administration: There should be some interface for editing configuration file [todo, usability]
* English translation [todo]
* Everything: probably use @inject instead of context [todo]
* Udates: use already downloaded files, from some safe storage - probably add option to disable online updates [todo, low priority]
* Backup: Get rid of phar dependency [low priority]
* Backup: upload progress bar [usability, eye candy, low priority]
* Backup: automatic backups [low priority]
* @layout: server switcher doesn't work on (my) mobile browser - check if my phone is really that stupid [low priority] 

DONE

* Permission: change to take in mind new uuid in ops.json (but it's little ugly)
* GameSettings: disable path and executable setting when common storage is in use [todo]
* Permissions: problem with ops without application account (they are deleted) [bug, confusing] (they are no longer deleted)
* GameSettings: check if user not changed the port number in server.properties [todo]
* Commands: make start/stop button bigger and more colorful [eye candy, usability] (just a little)
* All resources: check if everybody have access to the right places [todo]
* SystemConfig: create some more better(shorter) way how to access config [todo, top priority] (as array)
* Create: There are no way how to create new server [todo]
* freePort.sh: in unused (used in ServerCommander in little degrading way... )
* @layout: server switcher doesn't work on (my) mobile browser [todo] (I think my phone is just stupid)
* Authentification: check new trends in Nette 2.1 [low priority] (they aren't)
* Status: autorefresh of logs is loading logs of diferent server
* Backups: cannot be created in new server, probably permission error (not a permission error, backups folder was missing)
* Configuration: Edit all other classes to use config files [todo] (rewriten into more parts)
* ServerRepository: path in db have to be allways valid [bug]
* GameSettings and Create: regex for valid path [todo] (not great but working)
* GameSettings: crash when server config files do not exists [bug]
* VersionManager: crash when server path is invalid [not a bug, path should be valid]
* VersionManager: has harcoded sources [bug]
* VesionManager: check updates only on user request [speedup]
* Status: logs should be autoupdated in some interval
* Configuration: There should be file with app configuration eg. with path to apache accessible folder to save mc serves in. Or some limitations on server number and so on [todo]