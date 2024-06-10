# Farming Simulator ModHub project

## Važno

Ukoliko koristite defaultni port ili neki drugi port za pokretanje MariaDB, morate promijeniti port u [db_functions.php](./fetch_mods_script/db/database_functions/db_functions.php) unutar funkcije **openConnection**. Port možete efektivno promijeniti tako da promijenite samo parametar "localhost:8111" u "localhost" ukoliko koristite default SQL port (3306) ili u "localhost:[vaš port]" ukoliko ste pridjelili neki drugi port. 


