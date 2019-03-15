# Zexarel
My personal PHP Framework

Content:
  - Functions
    - [find](#find)
    - [get_string_between](#get_string_between)
    - [redirect](#redirect)
    - [d_var_dump](#d_var_dump)
  - Classes
    - [ZConfig](#ZConfig)
    - [ZDatabase](#ZDatabase)
    - [ZRoute](#ZRoute)
    - [ZView](#ZView)
    - [ZModel](#ZModel)

## find
```php
function find(string $what, string $in) : int
```
This function searches a string inside another string, it returns -1 if the string is not found, else it returns the position (0 is the first char)
## get_string_between
```php
function get_string_between(string $str, string $from, string $to) : string
```
This function returns the string between two selected strings
## redirect
```php
function redirect(string $location) : void
```
Returns an HTTP header that redirects to the location
## d_var_dump
```php
function d_var_dump(mixed $obj, int $size = null) : void
```
This function prints the obj content, you can choose the size (default 17px)
Find more info [here](https://github.com/Zexal0807/d_var_dump).

## ZConfig
This class manages the application config, to use this class you need to have a .zenv file where configs are saved.
In your application, when you want to get a config, you can call statically the config method, passing the config's key and the optional default value
```php
ZConfig::config("APP_NAME", "Zexarel");
```
## ZDatabase
This class manages the database, to use this class you must set the connection parameters in .zenv file, you then create a new class that extends ZDatabase and overrides the beforeExecute and afterExecute method:
```php
class My_Database extends ZDatabase{
  protected function beforeExecute($sql){
    //you can use a logger
  }
  protected function afterExecute($sql, $result, $rowAffected){
    //you can use a logger
  }
}
```
To create a SQL query you must create a new object, and apply the method on it:
```php
$db = new ZDatabase();
$ret = $db->select("*")
  ->from("users")
  ->where("id", "=", 1)
  ->execute();
  //Now $ret contains all field of table users where id = 1
```
Here there are all the possible methods:
```php
public function select(...$fields)
public function selectAll()
public function selectDistinct(... $fields)
public function from($table){
public function where($field, $operator, $compare)
public function groupBy($field)
public function having($field, $operator, $compare)
public function orderBy($field)
public function innerJoin($table, $on, $operator, $compare)
public function leftJoin($table, $on, $operator, $compare)
public function rightJoin($table, $on, $operator, $compare)
public function insert($table, ... $field_list)
public function value(... $value_list)
public function update($table)
public function set($field, $value)
public function getSQL() : string         //return the SQL string
public function execute() : void          //execute
public function executeSql($sql) : void   //execute a specific SQL
```

## ZRoute
## ZView
## ZModel
