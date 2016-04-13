Various tools for querying databases.

Examples with schema:
```sql
create table `car` (
  `car_id` int primary key not null auto_increment,
  `make` text not null,
  `model` text not null
);
```

```php
use Vinnia\Util\Database\PDODatabase;
use Vinnia\Util\Database\Helper;
use Vinnia\Util\Database\MysqlQuoter;

$dsn = 'mysql:host=127.0.0.1;dbname=my_db';
$db = new PDODatabase::build($dsn, 'user', 'pass');

// execute the query and fetch all rows
$cars = $db->queryAll('select * from cars');

// execute the query and fetch the first row
$car = $db->query('select * from cars');

// execute a statement without return value
$db->execute('insert into car(make, model) values (:make, :model)', $params = [
    ':make' => 'volvo',
    ':model' => 'xc90'
]);

$helper = new Helper($db, new MysqlQuoter());

// insert a row into the "car" table with the supplied data
$helper->insert('car', $values = ['make' => 'volvo', 'model' => 'xc90']);

// update rows in the "car" table with the supplied data where the predicate is true
$helper->update('car', $values = ['model' => 'v70'], $predicate = ['make' => 'volvo']);

// select all rows from the "car" table
$allCars = $helper->select('car');

// select the first row from the "car" table
$oneCar = $helper->selectOne('car');

// select all rows from the "car" table where the predicate is true
$volvos = $helper->select('car', $columns = ['*'], $predicate = ['make' => 'volvo']);

// insert or update a row in the "car" table depending on wether the predicate is true
$helper->insertOrUpdate('car', $values = ['make' => 'volvo', 'model' => 'xc60'], $predicate = ['make' => 'xc90'])

```
