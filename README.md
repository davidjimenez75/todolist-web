# todolist-web

PHP script to create a single CSV file joining all the CSV LOGS from your projects.

You can download TodoList for Windows here:  

- http://www.abstractspoon.com


## OPTIONS

- screen               HTML table with all the tasks.
- screen_year          HTML table with all the tasks ended on a year.
- screen_year_month	   HTML table with all the tasks ended on a year and month.
- download             Download a CSV with all the tasks.
- download_year        Download a CSV with all the tasks ended on a year.
- download_year_month  Download a CSV with all the tasks ended on a year and month.
- dokuwiki             Creates a DokuWiki table with all the tasks.
- listcsv              List all files (*.csv)
- listtdl              List all files (*.tdl)


## INSTALLATION


Firstly you need to install the PHP dependencies.

```
composer install
```

If you want to open TodoList files from your browser (tdl://*.tdl) modify the var $rootDir in the index.php 

By default the script is asuming you have your TodoList *.tdl files on "C:\TODOLIST"

### Laragon

Execute as administrator:

```
mklink /d c:\laragon\www\todolist c:\TODOLIST
```

http://todolist.test/


### XAMPP

Execute as administrator:

```
mklink /d c:\xampp\htdocs\todolist c:\TODOLIST
```

http://localhost/todolist/
