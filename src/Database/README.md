Various tools for querying a mysql database.

Some examples:
```php
$dsn = 'mysql:host=127.0.0.1;dbname=my_db';
$db = new \Vinnia\Util\Database\PDODatabase::build($dsn, 'user', 'pass');

$cars = $db->queryAll('select * from cars');

$car = $db->query('select * from cars');

$db->execute('insert into car(make, model) values (:make, :model)', $params = [
    ':make' => 'volvo',
    ':model' => 'xc90'
]);

$helper = new \Vinnia\Util\Database\Helper($db);

$helper->insert('car', $values = ['make' => 'volvo', 'model' => 'xc90']);

$helper->update('car', $values = ['model' => 'v70'], $predicate = ['make' => 'volvo']);

$allCars = $helper->select('car');

$oneCar = $helper->selectOne('car');

$volvos = $helper->select('car', $columns = ['*'], $predicate = ['make' => 'volvo']);

$helper->insertOrUpdate('car', $values = ['make' => 'volvo', 'model' => 'xc60'], $predicate = ['make' => 'xc90'])

```
