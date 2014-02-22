TODO

* GameSettings: check if user not changed the port number in server.properties [todo]
* GameSettings: disable path and executable setting when common storage is in use
* freePort.sh: in unused 
* None: There is no way how to delete server [todo]
* Config and VersionManager: find way how to use more regex variants [todo]
* Permissions: problem with ops without application account (they are deleted) [bug, confusing]
* Create: There are no way how to create new server [todo]
* Administration: There should be some interface for editing configuration file [todo, usability]
* Udates: use already downloaded files, from some safe storage - probably add option to disable online updates [todo, low priority]
* Backup: Get rid of phar dependency [low priority]
* Backup: upload progress bar [usability, eye candy, low priority]
* Backup: automatic backups [low priority]
* @layout: server switcher doesn't work on (my) mobile browser - check if my phone is really that stupid [low priority] 

DONE

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