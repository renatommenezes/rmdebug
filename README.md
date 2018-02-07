
# RmDebug : Easy debug for PHP
A project written by renatommenezes

## About
RmDebug is a class for use in project php, easy and simple for use.
RmDebug requires PHP >= 5.3
## How Use
RmDebug is a static class and can be used as follows:

```php
<?php
	include "debug.php";
	// script start
	Debug::start();
	
	// end of script
	echo Debug::show();
?>
```
For show Debug press key "CTRL" twice on your keyboard  that will open a modal with the initial information of the debug:

- Benchmarck
- GET
- POST
- PAGES
- LOG

## Document

### Method db(string $query)
Used to display query, can be stored several querys.
```php
<?php
	Debug::db("SELECT * FROM user");
	Debug::db("SELECT * FROM person");
?>
```
### Method destroy(void)

Used to finish debug execution.
```php
<?php
	Debug::destroy();
?>
```

### Method custom(string|array $mensage, $data = [])

Used to show any text message or data array.
```php
<?php
	Debug::custom("Lorem ipsum");
	Debug::custom("Lorem ipsum", array("id" => 1, "name" => "Saulo"));
	Debug::custom(array("id" => 2, "name" => "Jonatan"));
?>
```
Result

```text
Lorem ipsum
Lorem ipsum: [id] => 1, [name] => Saulo
[id] => 2, [name] => Jonatan
```

### Method logInfo(string|array $mensage, $data = [])

Used to show script execution message.
```php
<?php
	Debug::logInfo("Saving user data Saulo.");
?>
```