# Class GUID.php

### STATIC FUNCTIONS

---
### GUID::generate(int $count, bool $lowerCase)
Generate a new GUID

@param `int` $count - default `1` How many GUIDs should be generated  
@param `bool` $lowerCase - default `true` for lowercase letters, otherwise in uppercase letters
@return `string|string[]` - If parameter count is 1, a string is returned, otherwise an array is returned

```php
echo GUID::generate(); // dc33c32f-f1d4-493c-a5c2-121dbe5df063
echo GUID::generate(count: 2, lowerCase: false); // ['4F90437D-74E4-4770-98CF-00DCD409C977', '923FCE07-9717-4E69-AF0E-CAD4278752B9']
```
---
### GUID::isValid(string $guid)
Check whether the GUID is valid

@param `string` $guid the guid as string  
@return `bool`

```php
GUID::isValid("dc33c32f-f1d4-493c-a5c2-121dbe5df063"); // true
GUID::isValid("hello-world"); // false
```
---
### GUID::isDefault(?string $guid)
Check whether the GUID is protected and contains only zeros

@param `string` $guid  
@return `bool`
```php
GUID::isDefault("00000000-0000-0000-0000-000000000000"); // true
GUID::isDefault(null); // false
GUID::isDefault(""); // false
GUID::isDefault("dc33c32f-f1d4-493c-a5c2-121dbe5df063"); // false
```
---