# php-csv
Simple CSV Reader/Writer as a learning project.

Please be careful if you're using this project in production. It is possible that the logic will be changed from one day to another :-/

You can use like this:
```php
$reader = new Reader($filepath);
// now set the strategy how to read the csv
$reader->getStrategy()
    ->setDelimiter("\t")
    ->setEncoding(CharsetEncoding::ISO_8859_1) // the encoding of the file
    ->setHasHeader(false) // if csv doesn't contain header
    ->setSkipEmptyRecords(true);

// you have also the possibility to instances a new Strategy object
$strategy = Strategy::create(); // with default values

```
[to be continue]
