<?php

/*

EN-US: The "configs capsule" file is used to store the configuration of how the database should be created. Below is an example of use.
-
PT-BR: O arquivo "configs capsule" é usado para armazenar a configuração de como o banco de dados deve ser criado. Abaixo está um exemplo de uso.

*/

/*-----------------------------------------------------------------------------------------------------------------------------------------------------

use Illuminate\Support\Facades\DB;

DB::statement('DROP TABLE IF EXISTS uploads');
DB::statement('DROP TABLE IF EXISTS users');

require_once "users_capsule.php";
require_once "uploads_capsule.php";

$pdo = DB::connection()->getPdo();
$pdo->exec("
CREATE TRIGGER `trigger_insert` AFTER INSERT ON `uploads` FOR EACH ROW UPDATE users SET users.change_uploads = 1 WHERE users.dir_user = NEW.dir_user;
CREATE TRIGGER `trigger_update` AFTER UPDATE ON `uploads` FOR EACH ROW UPDATE users SET users.change_uploads = 1 WHERE users.dir_user = NEW.dir_user;
CREATE TRIGGER `trigger_delete` AFTER DELETE ON `uploads` FOR EACH ROW UPDATE users SET users.change_uploads = 1 WHERE users.dir_user = OLD.dir_user;
");

-----------------------------------------------------------------------------------------------------------------------------------------------------*/

use App\Models\Contact;
use Ramsey\Uuid\Uuid;

require_once "activitylogs_capsule.php";
require_once "contacts_capsule.php";

for ($i = 1; $i <= 35; $i++) {
    $contact = new Contact();
    $contact->uuid = Uuid::uuid7();
    $contact->name = "name" . $i;
    $contact->email = "name" . $i . "@mail.com";
    $contact->save();
}
