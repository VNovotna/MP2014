TODO

* None: There is no way how to delete server [todo]
* Config and VersionManager: find way how to use more regex variants [todo]
* Permissions: problem with ops without application account (they are deleted) [bug, confusing]
* Create: There are no way how to create new server [todo]
* Configuration: Edit all other classes to use config files [todo]
* Administration: There should be some interface for editing configuration file [todo, usability]
* Backup: Get rid of phar dependency [low priority]
* Authentification: check new trends in Nette 2.1 [low priority]
* Backup: upload progress bar [usability, eye candy, low priority]

DONE

* ServerRepository: path in db have to be allways valid [bug]
* GameSettings and Create: regex for valid path [todo] (not great but working)
* GameSetting: crash when server config files do not exists [bug]
* VersionManager: crash when server path is invalid [not a bug, path should be valid]
* VersionManager: has harcoded sources [bug]
* VesionManager: check updates only on user request [speedup]
* Status: logs should be autoupdated in some interval
* Configuration: There should be file with app configuration eg. with path to apache accessible folder to save mc serves in. Or some limitations on server number and so on [todo]