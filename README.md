# phpetl
**This README file is for the previous version of PhpETL. The source code has been rewritten and you should keep the following points in mind:**

1. CSV support is still in progress for the new version.
2. The class names have been changed, as well as the method signatures.
3. The project is now using namespaces.

**Write ETL Scripts in PHP**

PhpETL provides a DataTable object that can represent a database table or a CSV file (or both). All you need to do is declare:

1. The source location and format.
2. The destination location and format.
3. Any transformations you want to perform.

**Basic Usage**

```php
// Basic example
include 'phpetl/DataTable.php';

$source_name = 'person';
$source = array (
    'header'    =>  array('id', 'name', 'age', 'city'),
    'filename'  =>  'before.csv',
    'path'      =>  '/path/to/files/in/',
);

$destination = array (
    'filename'  =>  'after.csv',
    'path'      =>  '/path/to/files/out/',
);

$data = new DataTable();
$data->extract($source, $source_name);
$data->transformTable('SELECT id, name, age, city FROM person ORDER BY CAST(age AS integer)');
$data->load($destination);
$data->close();
```

before.csv:

| id  | name    | age | city      |
| --- | ------- | --- | --------- |
| 1   | Evan    | 22  | New York  |
| 2   | Hayley  | 84  | Phoenix   |
| 3   | Melvin  | 7   | San Diego |
| 4   | Jane    | 45  | Dallas    |
| 5   | Charlie | 61  | Chicago   |

after.csv:

| id  | name    | age | city      |
| --- | ------- | --- | --------- |
| 3   | Melvin  | 7   | San Diego |
| 1   | Evan    | 22  | New York  |
| 4   | Jane    | 45  | Dallas    |
| 5   | Charlie | 61  | Chicago   |
| 2   | Hayley  | 84  | Phoenix   |

**Transformations**

You can perform manual transformations using the *transformTable()* method, which takes a SQLite query (SQLite is the intermediate storage format). Multiple transformations can be performed before the data is written to the output file.

There is one built-in transformation that allows you to split columns. This can be useful with tables that are not normalized, such as EAV (entity-attribute-value). The values can be combined by first performing a GROUP_CONCAT transformation, followed by splitting the column. Most RDBMS vendors don't support column splitting, so *splitColumn()* can be used:

```php
$data->splitColumn('weight_height', ';', array ('weight', 'height'));
```

**Iteration**

DataTable implements the Iterator interface, so you can loop through it and modify individual values:

```php
foreach($data as $row) {
    $row['name'] = 'unknown';
    $data->transformRow($row);
}
```

**Database Connection**

To declare a MySQL table source:

```php
$source = array (
    'host'      =>  'my_host',
    'user'      =>  'my_user',
    'password'  =>  'my_pass',
    'database'  =>  'my_db',
    'sql'       =>  $sql // Optional
);
```

MySQL destinations must also include *fields* and *update_fields*:

```php
$destination = array (
    'host'      =>  'my_host',
    'user'      =>  'my_user',
    'password'  =>  'my_pass',
    'database'  =>  'my_db',
    'table'     =>  'my_table',
    // The first field in intermediate storage will be mapped to 'field1' in the destination table, etc.
    'fields'    =>  'field1, field2, field3',
    // If the row already exists, update these fields. Do not update 'field1'.
    'update_fields'    =>  'field2, field3',
);
```
