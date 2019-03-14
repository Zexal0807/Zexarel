# Zexarel
My personal PHP Framewotk

Content:
  - Function
    - [find](#find)
    - [get_string_between](#get_string_between)
    - [redirect](#redirect)
    - [d_var_dump](#d_var_dump)


## find
```php
function find(string $what, string $in) : int
```
This function search a string inside other string, it return -1 if the string was not found else return the position (0 is the first char)
## get_string_between
```php
function get_string_between(string $str, string $from, string $to) : string
```
This function return the string between two selected string
## redirect
```php
function redirect(string $location) : void
```
Return a HTTP header that redirect to the location
## d_var_dump
```php
function d_var_dump(mixed $obj, int $size = null) : void
```
This function print the obj content, you can choose the size (deafult 17px)
Find more info [here](https://github.com/Zexal0807/d_var_dump).
