#PHPDB

Provides a wrapper for the PHP PDO class to help make MySQL connectins and transactions simpler.

---

##Properties

| Name             | Type   | Description                                           |
|------------------|--------|-------------------------------------------------------|
| **executed**     | bool   | If the last query was successfully executed or not.   |
| **isConnected**  | bool   | If the handler is currently connected to the database |
| **lastInsertId** | string | The last insert ID.                                   |
| **rowCount**     | int    | The number of rows affected from the last query.      |

---

##Usage

Connecting:

```php
$db = new DB($db_host, $db_name, $db_user, $db_pass);
```

Query (without results):

```
$query = "INSERT INTO table (column) VALUES ('value');";

$db->execute($query);
```

Query (with results):

```
$query = "SELECT :a as a, :b as b, :c as c;";

// Param types can be one of following
// PARAM_BOOL | PARAM_NULL | PARAM_INT | PARAM_STR | PARAM_LOB
// See: http://php.net/manual/en/pdo.constants.php

$binds = array(
	array('bind' => 'a', 'value' => 'potato'),
	array('bind' => 'b', 'value' => 'carrot'),
	array('bind' => 'c', 'value' => 'corn',   'type' => PDO::PARAM_STR) // <- Optionally specify PDO data type
);

if ($results = $db->execute($query, $binds)) {

	// Select results from the first row (index 0)
	$a = $results[0]['a'];
	$b = $results[0]['b'];
	$c = $results[0]['c'];
	
	echo "{$a}, {$b}, {$c}";
}
```
