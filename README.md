# Farming Simulator ModHub project

## Važno - promjena server SQL porta kod pokretanja projekta

Ukoliko koristite defaultni port ili neki drugi port za pokretanje MariaDB, morate promijeniti port u [db_functions.php](./fetch_mods_script/db/database_functions/db_functions.php) unutar funkcije **openConnection**. Port možete efektivno promijeniti tako da promijenite samo parametar "localhost:8111" u "localhost" ukoliko koristite default SQL port (3306) ili u "localhost:[vaš port]" ukoliko ste pridjelili neki drugi port. 

## Instalacija baze podataka - 15 MB (9917 artikala modifikacija)

[Export baze podataka](./pwa_project_db.sql) - export baze u SQL query obliku, importa se preko "Import" tab-a na /localhost/phpmyadmin -u.

### Login za Admin korisnički račun

Ukoliko želite isprobati admin mogućnosti.

**Korisničko ime** - adminacc
**Lozinka** - adminpass

### Login za normalni korisnički račun

**Korisničko ime** - korisnik
**Lozinka** - korisnikpassword