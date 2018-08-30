/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/


INSTRUCTIONS TO UPGRADE AN EXISTING todoyu VERSION
==================================================

1. Make sure you have a backup of your documents (in /files) and your database. They should stay untouched, but you never know...
2. Backup your config files (in /config). (db.php, extensions.php, etc). You have to restore them later
3. Delete the content of the cache folder
4. Overwrite the todoyu scripts with the new ones you have downloaded
5. The config files were also overwritten in step 4. Restore them with your backup files from step 2
6. Open todoyu in your browser, you will be redirected to the update dialog. (Browser should go to /install)
7. Follow the update instructions.
8. Enjoy the new features ;-)