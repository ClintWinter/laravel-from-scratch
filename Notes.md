# Laravel 5.4 From Scratch

This is the notes for a video series on laravel called "Laravel 5.4 From Scratch" done by Jeff Way from Laracasts.

## Episode 1: Laravel Installation and Composer

Just watch it. To create a project:

```
$ laravel new blog
```

to modify the path for a new mac device, do

```
export PATH=$PATH:$HOME/.composer/vendor/bin
```


## Episode 2: Basic Routing and Views

To start up our project in laravel, we can use **php artisan**.

```
$ php artisan serve
```

This creates a dev server for us to display our new app.

The first place we will go is `routes/web.php`. This is the main file where we handle all of our routing.

``` PHP
// routes/web.php

Route::get('/', function () {
    return view('welcome');
});
```

This is saying, if we get the path `'/'`, we will return the view `'welcome'`. This should look similar to when we made our own framework. The views that this file serves up is located in `resources/views/`. You will see `welcome.blade.php` in there already, which is what this route currently is returning.

**Blade** is laravel's template engine. It allows you to work with PHP in a cleaner fashion in your views.

Let's add another route to our `web.php` file.

``` PHP
// routes/web.php

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function() {
    return view('about');
});
```

We can omit the `.blade.php` suffix from the view name. Next create the view in `resources/views/about.blade.php`.

``` HTML
<!-- resources/views/about.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>About</title>
</head>
<body>

    <h1>About Us</h1>

</body>
</html>
```

This will work.


## Episode 3: Laravel Valet is your Best Friend

Skipping this.


## Episode 4: Database Setup and Sequel Pro

To setup our project to use MySQL, we can start by preparing the database.

Use the terminal to go log onto our mysql CLI:

```
mysql -uroot -p
```

Then create a database for the project:

```
mysql> create database blog;
```

In your laravel project you'll have a `/.env` file. This is a secure place to store keys, passwords, etc. This would also be created separately on a production server providing it's own production keys and passwords. In this file you'll see the `DB_*` key/value pairs. This is where we can input our information to use a database.

``` .env
; /.env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=blog
DB_USERNAME=root
DB_PASSWORD=root
```

This should work for connecting to the database we created. 8889 in this instance is the same as the mamp pro mysql port while running. I'm not sure if it works because of that or in spite of that.

To prevent an error that will occur when migrating the tables, we should change `app/Providers/AppServiceProvider.php` to look like this:

``` PHP
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
```

 Next we can run

```
$ php artisan migrate
```

to migrate our tables. Laravel includes a couple tables that all projects will use, and `migrate` turns the php schema that we have in our project into actual tables in our database. If we go back to mysql

```
mysql> show tables;
```

we can see our newly created tables:

```
+-----------------+
| Tables_in_blog  |
+-----------------+
| migrations      |
| password_resets |
| users           |
+-----------------+
```

And at this point our application is hooked up to our database.


## Episode 5: Pass Data to Your Views

Let's clear our welcome view and replace it with a basic html skeleton that displays a name.

``` PHP
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome</title>
</head>
<body>
    
    <h1>Hello, <?= $name; ?></h1>

</body>
</html>
```

Right now the name variable, obviously, does not exist. In `routes/web.php`

``` PHP
// routes/web.php

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function() {
    return view('about');
});
```

we return the view but we don't give it any data. We can do this:

``` PHP
Route::get('/', function () {
    return view('welcome', [
        'name' => 'World'
    ]);
});
```

and this will render the page as "Hello, World". This is also possible:

``` PHP
Route::get('/', function () {
    return view('welcome')->with('name', 'World');
});
```

And a third option:

``` PHP
Route::get('/', function () {
    
    $name = "World";
    $age = 25;
    
    return view('welcome', compact('name', 'age'));
});
```

Let's make the data some tasks:

``` PHP
Route::get('/', function () {
    
    $tasks = [
        'go to the store',
        'finish tutorial',
        'go to the gym'
    ];
    
    return view('welcome', compact('tasks'));
});
```

In our view we can loop through that data the standard way like so:

``` PHP
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome</title>
</head>
<body>
    
    <ul>
    <? foreach ($tasks as $task) { ?>
        <li><?=$task?></li>
    <? } ?>
    </ul>

</body>
</html>
```

PHP might be a templating language itself, but it's quite verbose. Let's use the power of blade to make things cleaner for us.

``` PHP
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome</title>
</head>
<body>
    
    <ul>
        @foreach ($tasks as $task)
            <li>{{ $task }}</li>
        @endforeach
    </ul>

</body>
</html>
```

And this will work.

This syntax works for other stuff too:

```
@if
@endif

@while
@endwhile
```

[https://laravel.com/docs/5.7/blade](More on blade templates)


## Episode 6: Working With the Query Builder

In real life we aren't hard-coding our tasks like we did in the last episode. They are being stored in a database.

We already have two tables created based on our migration files which are stored in `database/migrations/`. They are `<date>_create_users_table.php` and `<date>_create_password_resets_table.php`.

Looking at the users file, we can see we have an `up` method and a `down` method. For our own migrations we don't have to manually write these files. PHP artisan comes in here.

To learn about different commands that comes with php artisan we can do `$ php artisan` to show a list of commands and a brief description of what they do. To get details about how a specific command works we can do `$ php artisan help <command>`, so for example `$ php artisan help make:migration` to learn about making a migration file.

To make a migration we have to give it some information:

```
$ php artisan make:migration <name> --create = <table>
```

The should look the way the name of the file looks for users/passwords. So `create_users_table` can be `create_tasks_table`.

So if we want to make a tasks table it'll look like this:

```
$ php artisan make:migration create_tasks_table --create=tasks
```

Once we run that command the file will appear in our `database/migrations/` folder.

``` PHP
// database/migrations/2018_09_07_121635_create_tasks_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}

```

We can now write the schema to design our tasks table before we migrate it.

``` PHP
public function up()
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->increments('id');
        $table->text('body');
        $table->timestamps();
    });
}
```

So we've prepared a migration, but the table doesn't exist yet because we haven't migrated yet. Let's do that now.

```
$ php artisan migrate
```

Now (using blog db):

```
mysql> show tables;
+-----------------+
| Tables_in_blog  |
+-----------------+
| migrations      |
| password_resets |
| tasks           |
| users           |
+-----------------+

mysql> desc tasks;
+------------+------------------+------+-----+---------+----------------+
| Field      | Type             | Null | Key | Default | Extra          |
+------------+------------------+------+-----+---------+----------------+
| id         | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| body       | text             | NO   |     | NULL    |                |
| created_at | timestamp        | YES  |     | NULL    |                |
| updated_at | timestamp        | YES  |     | NULL    |                |
+------------+------------------+------+-----+---------+----------------+
```

If we make a mistake in our schema and want to change the table. We can add or remove a column or change a data type, then refresh the migration.

```
$ php artisan migrate:refresh
```

This will roll back all the migrations the remigrate them.

Let's populate the table manually with a couple tasks.

```
mysql> insert into tasks (body, created_at, updated_at) VALUES ('Go to the store', NOW(), NOW());

mysql> insert into tasks (body, created_at, updated_at) VALUES ('Finish laravel tutorial', NOW(), NOW());

mysql> select * from tasks;
+----+-------------------------+---------------------+---------------------+
| id | body                    | created_at          | updated_at          |
+----+-------------------------+---------------------+---------------------+
|  1 | Go to the store         | 2018-09-07 08:24:28 | 2018-09-07 08:24:28 |
|  2 | Finish laravel tutorial | 2018-09-07 08:24:37 | 2018-09-07 08:24:37 |
+----+-------------------------+---------------------+---------------------+
```

Now we want to fetch our data to use in our project. Go to `routes/web.php`.

``` PHP
// routes/web.php

Route::get('/', function () {
    
    $tasks = DB::table('tasks')->get();
    
    return view('welcome', compact('tasks'));
});
```

Fun fact, if you ever return a database query from a route, laravel will automatically turn it into JSON, which is great for APIs:

``` PHP
// routes/web.php

Route::get('/', function () {
    
    $tasks = DB::table('tasks')->get();
    
    return $tasks;
});
```

Now, passing our tasks to the view, we need to handle the data a little differently. `$tasks` is now an array of objects, so to display our tasks we need to use the body column like so:

``` PHP
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome</title>
</head>
<body>
    
    <ul>
        @foreach ($tasks as $task)
            <li>{{ $task->body }}</li>
        @endforeach
    </ul>

</body>
</html>
```

This

``` PHP
DB::table('tasks')->get();
```

is laravel's query builder. It allows us to add any number of conditions, for example:

``` PHP
DB::table('tasks')->where('completed', true)->get();
DB::table('tasks')->where('created_at', '>=', '2018-04-02')->get();
```

or we can order a certain way:

``` PHP
DB::table('tasks')->latest()->get();
```

Maybe we want to look for a certain task. We can make a route to handle that:

``` PHP
// routes/web.php

Route::get('/tasks/{id}', function() {
    
});
```

Notice a key surrounded by curly braces `{id}`. In laravel, this is called a **wildcard**. This means the value can be anything `/tasks/5` or `/tasks/20`.

``` PHP
// routes/web.php

Route::get('/tasks/{task}', function() {
    
});
```

Now let's accept the ID and dump it:

``` PHP
// routes/web.php

Route::get('/tasks/{task}', function($id) {
    dd($id);
});
```

If we go to `/tasks/1` or whatever the id is of a current tasks, we will see the id printed in the browser.

We could do this:

``` PHP
Route::get('/tasks/{task}', function($id) {
    $task = DB::table('tasks')->find($id);
    dd($task);
});
```

and that will give us the whole task object. So next maybe we pass this to a certain view.

``` PHP
Route::get('/tasks/{task}', function($id) {
    $task = DB::table('tasks')->find($id);
    
    return view('tasks.show', compact('task'));
});
```

`'tasks.show'` is the syntax for subfolders. So that basically translates to `reources/views/tasks/show.blade.php`.

``` PHP
// resources/views/tasks/show.blade.php

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task</title>
</head>
<body>
        <h1>{{ $task->body }}</h1>
</body>
</html>
```

And this will work! Let's rewrite our routes a bit:

``` PHP
Route::get('/tasks', function () {
    
    $tasks = DB::table('tasks')->get();
    
    return view('tasks.index', compact('tasks'));
});

Route::get('/tasks/{task}', function($id) {
    $task = DB::table('tasks')->find($id);
    
    return view('tasks.show', compact('task'));
});
```

Now `/tasks` should be the route fetching all of our tasks and that should go to the index page of our tasks view folder.

In `resources/views/tasks/index.blade.php` we can make each of our list items a link that goes to the show page of that task.

``` PHP
// resources/views/tasks/index.blade.php

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome</title>
</head>
<body>
    
    <ul>
        @foreach ($tasks as $task)
            <li>
                <a href="/tasks/{{$task->id}}">{{ $task->body }}</a>
            </li>
        @endforeach
    </ul>

</body>
</html>
```

And in `resources/views/tasks/show.blade.php` we can add a link back to our tasks:

``` PHP
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task {{ $task->id }}</title>
</head>
<body>
        <p>
            <a href="/tasks">View all tasks</a>
        </p>

        <h1>{{ $task->body }}</h1>
</body>
</html>
```

## Episode 7: Eloquent 101

A **modle** is a representation of something in your system.

In our case we want the representation of a task. So let's make a model using php artisan.

```
$ php artisan make:model Task
```

Now a file will be in `app/` called `app/Task.php`.

Models use Laravel's Eloquent model as seen at the top of the new file.

``` php
use Illuminate\Database\Eloquent\Model;
```

To show how it works we will use `$ php artisan tinker`, which gives us a laravel shell to interact with the application.

```
>>> App\Task::all()
```

Using our new model here, we can fetch all of the tasks with this simple command.

What if we want tasks with an id of 2 or greater?

```
>>> App\Task::where('id', '>', 1)->get()
```

Maybe we want to fetch the body on every task.

```
>>> App\Task::pluck('body');
```

Or just the first body

```
>>> App\Task::pluck('body')->first()
```

Let's use it in our project.

``` PHP
// routes/web.php

Route::get('/tasks', function () {
    
    // $tasks = DB::table('tasks')->get();
    $tasks = App\Task::all();
    
    return view('tasks.index', compact('tasks'));
});
```

So we've switched over from QueryBuilder to using Eloquent in a dedicated class. What's nice is we can add any number of methods to the model to add additional behavior.

Maybe we want to see if tasks are completed:

``` PHP
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public function isComplete()
    {
        return false;
    }
}

```

Let's see how it works in tinker:

```
>>> $task = App\Task::first()
```

This returns an instance of our Task class, which means we can use any methods on it that we added to our class.

```
>>> $task->isComplete()
=> false
```

Let's move our show route to eloquent as well.

``` PHP
Route::get('/tasks', function () {
    
    // $tasks = DB::table('tasks')->get();
    $tasks = App\Task::all();
    
    return view('tasks.index', compact('tasks'));
});

Route::get('/tasks/{task}', function($id) {
    // $task = DB::table('tasks')->find($id);
    $task = App\Task::find($id);
    
    return view('tasks.show', compact('task'));
});
```

Let's also add a use statement so we don't have to type `App\Task` every time.

``` PHP

use App\Task;

Route::get('/tasks', function () {
    
    // $tasks = DB::table('tasks')->get();
    $tasks = Task::all();
    
    return view('tasks.index', compact('tasks'));
});

Route::get('/tasks/{task}', function($id) {
    // $task = DB::table('tasks')->find($id);
    $task = Task::find($id);
    
    return view('tasks.show', compact('task'));
});
```

So the first time we did all this, we did a migration, then created our model. But we can actually do them at the same time.

To show an example 

```
$ php artisan migrate:reset
Rolling back: 2018_09_07_121635_create_tasks_table
Rolled back:  2018_09_07_121635_create_tasks_table
Rolling back: 2014_10_12_100000_create_password_resets_table
Rolled back:  2014_10_12_100000_create_password_resets_table
Rolling back: 2014_10_12_000000_create_users_table
Rolled back:  2014_10_12_000000_create_users_table
```

and go ahead and delete the task migration file and task model file entirely and start over.

Here is the *recommended way of doing it*. We decide that we need a task and we also need a tasks table.

> Remember that the noun we use for our model/migration should be singular (Task, Post, Comment, etc.)

```
$ php artisan make:model Task -m
```

If we look at 

```
$ php artisan help make:model
```

we will see that the `-m` flag creates a migration file for the model. This is in the future, but you'll also see a `-c` flag for creating a controller, which we will also do soon. Laravel may look for old files that we deleted when we try to run our make:model command, so we can do

```
$ composer dump-autoload
```

to refresh before we create our model and migration. Now you'll see we have our migration file and our model in our project. We can modify our migration file once again to setup our Task as we see fit.

``` PHP
// database/migrations/2018_09_08_132601_create_tasks_table.php

Schema::create('tasks', function (Blueprint $table) {
    $table->increments('id');
    $table->text('body');
    $table->boolean('completed');
    $table->timestamps();
});
```

This time we add a completed field. Now let's migrate our database.

```
$ php artisan migrate
```

We can now use `php artisan tinker` to whip up a couple tasks easily so we can populate our database.

```
>>> $task = new App\Task;
>>> $task->body = 'Go to the market';
>>> $task->completed = false;
```

But wait, we don't want to have to manually set completed to false for every new task, a new task should automatically be false. Let's update our migration file schema.

``` PHP
// database/migrations/2018_09_08_132601_create_tasks_table.php

Schema::create('tasks', function (Blueprint $table) {
    $table->increments('id');
    $table->text('body');
    $table->boolean('completed')->default(false);
    $table->timestamps();
});
```

Let's refresh our migration to handle the change.

```
$ php artisan migrate:refresh
Rolling back: 2018_09_08_132601_create_tasks_table
Rolled back:  2018_09_08_132601_create_tasks_table
Rolling back: 2014_10_12_100000_create_password_resets_table
Rolled back:  2014_10_12_100000_create_password_resets_table
Rolling back: 2014_10_12_000000_create_users_table
Rolled back:  2014_10_12_000000_create_users_table
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table
Migrating: 2014_10_12_100000_create_password_resets_table
Migrated:  2014_10_12_100000_create_password_resets_table
Migrating: 2018_09_08_132601_create_tasks_table
Migrated:  2018_09_08_132601_create_tasks_table
```

Go back to tinker and try this again.

```
>>> $task = new App\Task;
>>> $task->body = 'Go to the store';
>>> $task->save();
```

Now if we refresh our `/tasks` page in the browser, you will see our new task there.

Let's add a couple more tasks and mark one of them completed.

```
>>> $task = App\Task::First();
>>> $task->completed = true;
>>> $task->save();
```

Maybe we only want to get tasks that haven't been completed yet.

```
>>> App\Task::where('completed', 0)->get();
```

But this looks a little wordy, we can add query scopes to our model to make the language cleaner for our queries. 

We have a couple of options. First is a static function:

``` PHP
// app/Task.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public static function incomplete()
    {
        return static::where('completed', 0)->get();
    }
}
```

Using it like so:

```
>>> App\Task::incomplete();
```

will give us only the incomplete tasks.


However, you may decide that you need to continue chaining, we can use query scopes for that:

``` PHP
// app/Task.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public function scopeIncomplete($query)
    {
        return $query->where('completed', 0);
    }
}
```

when we prefix this with scope, laravel knows to treat it as a query scope. It will accept the existing query and return the next chain, allowing it to keep going. The way this actually works is the query is passed, maybe it's a previous where statement which return it's class instance, then we return our where clause, which also returns an instance of itself, allowing it to continue.

```
>>> App\Task::incomplete()->get();

>>> App\Task::incomplete()->where('id', '>=', 2)->get();
```


## Episode 8: Controllers

**Controllers** are the middlemen. They receive a request, retrieve the data, format it appropriately, and pass it to the view or presentation layer.

Let's make our routes use controllers rather than the simple callback functions they currently use. When we get into bigger projects it becomes difficult to manage those simple functions.

The syntax looks like this:

``` PHP
Route::get('/path', 'ControllerClass@method');
```

The methods we use will follow the basic RESTful routing standard.

``` PHP
// routes/web.php

use App\Task;

Route::get('/tasks', 'TaskController@index');

Route::get('/tasks', function () {
    // $tasks = DB::table('tasks')->get();
    $tasks = Task::all();
    
    return view('tasks.index', compact('tasks'));
});

Route::get('/tasks/{task}', function($id) {
    // $task = DB::table('tasks')->find($id);
    $task = Task::find($id);
    
    return view('tasks.show', compact('task'));
});
```

Our controller file hasn't been created yet, but they belong in the folder `app/Http/Controllers/`. But of course we won't manually create this file, we can use a generator with php artisan.

```
$ php artisan make:controller TaskController
```

And here is what is created for us:

``` PHP
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    //
}
```

Let's give it our index method that will are using in our `routes/web.php` file. And everything in our current callback function for `/tasks` can go into our new `index` method. Don't forget to use `App\Task` at the top so we can still use our Task model.

``` PHP
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
    
        return view('tasks.index', compact('tasks'));
    }
}
```

Now our refactored `/tasks` route will work, let's do our second route.

``` PHP
// routes/web.php

use App\Task;

Route::get('/tasks', 'TaskController@index');
Route::get('/tasks/{task}', 'TaskController@show');


Route::get('/tasks/{task}', function($id) {
    // $task = DB::table('tasks')->find($id);
    $task = Task::find($id);
    
    return view('tasks.show', compact('task'));
});
```

Let's create our second controller method.

``` PHP
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
    
        return view('tasks.index', compact('tasks'));
    }

    public function show($id)
    {
        $task = Task::find($id);
    
        return view('tasks.show', compact('task'));
    }
}
```

Our new routes file looks much better, we can also remove the use statement at the top to clean it up more.

``` PHP
// routes/web.php

Route::get('/tasks', 'TaskController@index');
Route::get('/tasks/{task}', 'TaskController@show');
```

And now both of our routes will work like they did before.


## Episode 9: Route Model Binding

In our show method, instead of using the wildcard to get an id and finding the Task with Eloquent, we can just expect a Task as the argument and use it directly instead:

``` PHP
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
    
        return view('tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        // $task = Task::find($id);
    
        return view('tasks.show', compact('task'));
    }
}
```

The route does not change to make this happen, it still looks like this:

``` PHP
// routes/web.php

Route::get('/tasks', 'TaskController@index');
Route::get('/tasks/{task}', 'TaskController@show');
```

The key thing to understand is to make sure that the wildcard name matches up with the variable name. It looks based on the primary key and based on the model being hinted in the parameters of the method. So it's `(Task $task)`, meaning we are using the Task model to find a task with the primary key that matches the wildcard that was given. `Task::find(<wildcard>)` is done for you.

By default, laravel assumes the wildcard is the primary key. If we want to modify that we can reference it as a slug. Maybe we want it to be the title:

``` PHP
Route::get('/posts/some-post-title-slug')
```

And that is done by changing a method in that post's model. We will get there down the road.


## Episode 10: Layouts and Structure

If we want to add a resource to our project, like a css file, we need to add it to every single page. Or if we change it change it on every singe page. Instead we should have one page that all the views load into.

In our `resources/views/` directory we will add a `layout.blade.php` file.

``` HTML
<!-- resources/views/layout.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task Project</title>
</head>
<body>
    @yield('content')
</body>
</html>
```

We are using yield for where we want to **yield** additional content.

There's an even shorter shorthand for creating a model, a migration file, and a controller.

```
$ php artisan make:model Post -mc
```

and if we plan to do RESTful routing with it, we can add the `-r` flag to add all of the methods to the controller from the start.

```
$ php artisan make:model Post -mcr
```

Right now we have our task views spitting out the entire html skeleton, resources, etc. including the content we want. Using a layout file we can just give it the important content and let the layout file handle all that other stuff in one place.

We can remove everything besides what's in the body in `resources/views/index.blade.php` and replace it with this:

``` HTML
<!-- resources/views/tasks/index.blade.php -->

@extends('layout')

@section('content')

<ul>
    @foreach ($tasks as $task)
        <li>
            @if ($task->completed)
                <s><a href="/tasks/{{$task->id}}">{{ $task->body }}</a></s>
            @else
                <a href="/tasks/{{$task->id}}">{{ $task->body }}</a>
            @endif
        </li>
    @endforeach
</ul>

@endsection
```

We can do the same for `resources/views/tasks/show.blade.php`

``` HTML
<!-- resources/views/tasks/show.blade.php -->

@extends('layout')

@section('content')

<p>
    <a href="/tasks">View all tasks</a>
</p>

<h1>{{ $task->body }}</h1>

@endsection
```

Sometimes we have a nav or a footer that goes into the main layout page but we just want to keep the page cleaner and manageable. In that instance we use `@include` to just bring the html in from that specified page. I also added bootstrap to the page.

``` HTML
<!-- resources/views/layout.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task Project</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>

    @include('layouts.nav')

    <main class="container pt-5">
        @yield('content')
    </main>
</body>
</html>
```

We can make a `/resources/views/layouts/` folder to hold all of our partial files that have to do with the layout. Here's the nav:

``` HTML
<!-- resources/views/layouts/nav.blade.php -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="/tasks">All Tasks</span></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
```

And here are our new index and show files using bootstrap to make it cleaner:

``` HTML
<!-- resources/views/tasks/index.blade.php -->

@extends('layout')

@section('content')

<div class="row">
    <div class="col-12">

        <h1 class="display-1">All Tasks</h1>

        <ul class="list-group">
            @foreach ($tasks as $task)
                <li class="list-group-item">
                    @if ($task->completed)
                        <s><a href="/tasks/{{$task->id}}">{{ $task->body }}</a></s>
                    @else
                        <a href="/tasks/{{$task->id}}">{{ $task->body }}</a>
                    @endif
                </li>
            @endforeach
        </ul>


    </div>
<div>

@endsection
```

``` HTML
<!-- resources/views/tasks/show.blade.php -->

@extends('layout')

@section('content')

<h1>{{ $task->body }}</h1>
@if($task->completed)
    <p class="text-success">Complete</p>
@else
<p class="text-danger">Incomplete</p>
@endif

@endsection
```

What we can do is actually move the layout file into the layouts folder and rename it master. Then change the `@extends` from `layout` to `layouts.master` in our `index` and `show` files.

And we can go ahead and remove our `resources/views/welcome.blade.php` and `resources/views/about.blade.php` files because they no longer serve any purpose.


## Episode 11: Form Request Data and CSRF

We want to start being able to create tasks and display them dynamically.

We will use the `/create` route to get the form for creating a new task.

``` PHP
// routes/web.php

Route::get('/tasks', 'TaskController@index');
Route::get('/tasks/create', 'TaskController@create');
Route::get('/tasks/{task}', 'TaskController@show');
```

Then we will add the method to our controller.

``` PHP
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
    
        return view('tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function create()
    {
        return view('tasks.create');
    }
}
```

Then we can create our view and test it.

``` HTML
<!-- resources/views/tasks/create.blade.php -->

@extends('layouts.master')

@section('content')

<h1>Create a Task</h1>

@endsection
```

We can also add the link to our new nav.

``` HTML
<!-- resources/views/layouts/nav.blade.php -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/tasks">All Tasks</span></a>
                </li>
            </ul>
            <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/tasks/create">New Task</span></a>
                    </li>
                </ul>
        </div>
    </div>
</nav>
```

We can now start building out our form.

``` HTML
<!-- resources/views/tasks/creat.blade.php -->

@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-12">
        <h1>Create a Task</h1>

        <hr>

        <div class="d-flex justify-content-center">
            <form action="" class="col-6 bg-light p-3 rounded">
                <div class="form-group">
                    <label for="body">Body</label>
                    <input type="text" name="body" class="form-control">
                </div>

                <div class="form-group">
                    <label for="completed">Completed</label>
                    <input type="checkbox" name="completed" class="form-control">
                </div>

                <div class="form-group">
                    <input type="submit" value="Create Task" class="btn btn-primary btn-block">
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
```

Next we can set the form to post to the route we want. We want it to post to `/tasks` if we are following RESTful conventions.

``` HTML
<form method="POST" action="/tasks" class="col-6 bg-light p-3 rounded">
```

Then we can create our route.

``` PHP
// routes/web.php

Route::get('/tasks', 'TaskController@index');
Route::post('/tasks', 'TaskController@store');
Route::get('/tasks/create', 'TaskController@create');
Route::get('/tasks/{task}', 'TaskController@show');
```

The basic standard RESTful (Representational State Transfer) route setup is this:

| Method | Route | Description |
| --- | --- | --- |
| GET | `/tasks` | Get all tasks. |
| POST | `/tasks` | Create a new task. |
| GET | `/tasks/create` | Get form for new task. |
| GET | `/tasks/{id}` | Get details of a specific task. |
| GET | `/tasks/{id}/edit` | Get form to update a task. |
| PATCH | `/tasks/{id}` | Update a task. |
| DELETE | `/tasks/{id}` | Delete a task. |

Next let's create our store method in our controller.

What we want to do is:
* create a new task with our request data
* save it to the database
* and then redirect to the home page.

``` PHP
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
    
        return view('tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store()
    {
        dd(request()->all());
    }
}
```

We also need to add a **CSRF token** to the form to protect from **Cross Site Request Forgery**. 

> If we try to submit the form at this time, it will either give an error or time out.

``` HTML
<!-- resources/views/tasks/create.blade.php -->

<form method="POST" action="/tasks" class="col-6 bg-light p-3 rounded">
    {{ csrf_field() }}

    <div class="form-group">
        <label for="body">Body</label>
        <input type="text" name="body" class="form-control">
    </div>

    <div class="form-group">
        <label for="completed">Completed</label>
        <input type="checkbox" name="completed" class="form-control">
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Create Task</button>
    </div>
</form>
```

Once we add the `csrf_field()` at the beginning of the form, we will see our form data dumped onto the page.

We can handle the request in all different ways:

``` PHP
request()->all() // to get all the data
request('body') // to get the body only
request(['body', 'completed']) // to get multiple fields
```

Let's use this to work on our store method.

``` PHP
public function store()
{
    $task = new Task;
}
```

Make sure to use the model at the top of the file. And if not, make sure you begin the model with a backslash `\App\Task` because if you do `App\Task` it will tack that onto the end of the controller's namespace, which obviously won't exist.

There's a couple ways to structure the creation of our task, let's show the simplest one first.

``` PHP
public function store()
{
    $task = new Task;

    $task->body = request('body');

    if (request('completed') !== NULL)
        $task->completed = request('completed');

    $task->save();

    return redirect('/tasks');
    
}
```

We can phrase this in a different way:

``` PHP
public function store()
{
    $completed = 0;
    if (request('completed') !== NULL)
        $completed = request('completed');

    Task::create([
        'body' => request('body'),
        'completed' => $completed
    ]);

    return redirect('/tasks');
}
```

Except just doing this will cause a mass assignment exception and not work. This is because it's another security concern that is covered for you. It worries because some people will do this:

``` PHP
public function store()
{
    Task::create(request()->all());

    return redirect('/tasks');
}
```

And pass all the data without checking the fields or the data that's being passed.

To make certain fields valid we can go to our model and give it a `$fillable` property, giving it only the names of the fields we are okay with being mass assigned.

``` PHP
// app/Task.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['body', 'completed'];
}
```

And this will now work.

However, using fillable is kind of annoying, so we have other options. One is to use `$guarded`, which is the inverse. It blacklists those field names rather than whitelists them. We can give it an empty array to allow any fields.

``` PHP
// app/Task.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];
}
```

What we can do instead of doing this for every single class, is we can create a parent class for all of our models that does it once. We alias Model as eloquent, then name our class "Model" extending "Eloquent".

``` PHP
// app/Model.php

namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    protected $guarded = [];
}

```

Now `app/Task.php` can look like this:

``` PHP
// app/Task.php

namespace App;

class Task extends Model
{

}
```

We removed the use statement because we are extending our own model instead of the eloquent model. So now it's up to you to remember to only pass the fields you are comfortable submitting to the server.

Now we can create our task this way, and this is probably the best way to do it:

``` PHP
public function store()
{
    Task::create(request(['body', 'completed']));

    return redirect('/tasks');
}
```

And now we have an efficient and compact way of submitting and handling a form!


# Episode 12: Form Validation 101

