# php-csv
Simple CSV Reader/Writer as a learning project.

## Set the options with an Strategy for Read and Write
* Basic features: Read CSV with different Delimiters, Enclosures, Escapes
* Extra Options: 
** set the Encoding for read/write csv. (Read file with given encoding, Write file with given encoding)
** skip a number of leading lines (read only)
** do you want to skip empty Records? (read only)
** has the csv a header?

## Functionality of the Reader
* Set that the Reader should normalize the header (Replacing unknown Chars or replace WS with underscore etc)
* read only the Header
* count the Fields
* read one Record
* read all Records
* read a Record at a Line Index (zerobased without header)
* read Records from a Range n to n (zerobased without header)

Usage of the Reader:
```php
// Reader must be construct with the existing file and it creates an Default Strategy in the constructor
$reader = new Reader($filepath);
/*
 * Default Strategy is
 * Delimiter = ',' 
 * Enclosure = '"'
 * Escape = "\\"
 * Encoding = "UTF-8"
 * hasHeader = true
 * skipEmptyLines = true
 * skipLeadingLines = 0
 * asAssociative = false (each records as ['headerfield' => value, ...])
 */

// now manipulate the strategy how to read your csv
$reader->getStrategy()
    ->setDelimiter("\t")
    ->setEncoding(CharsetEncoding::ISO_8859_1) // the encoding of the file
    ->setHasHeader(false) // if csv doesn't contain header
    ->setSkipEmptyRecords(true);

// you have also the possibility to instances a new Strategy object
$strategy = Strategy::create(); // with default values
// Than you can set the Strategy
$reader->setStrategy($strategy);

// if you want to read one Record
$record = $reader->readRecord();
// will return the first record
$nextRecord = $reader->readRecord();
// will return the next record

// you can also use an while loop
while (null !== ($record = $reader->readRecord())) {
    // ... do your stuff
}
```

# The Writer

The Writer uses the same Strategy, Encoding etc as the Reader. Also the usage is very simple.

## Options for Write take a look at the top of this File
...

## Functionality of the Writer
* instantiate the writer with targetPath (optional), filename (optional) 
and if you want to append (default false) the content to the file
* Writer instantiates the standard Strategy in the constructor
* set the Header with an array of strings which are not empty (null or '')
* you can normalize the Header with the HeaderNormalizer
* write only one Record (Write the record to temporary file)
* write an array of Records (Write the records to temporary file)
* and write Records to csv File
* get Tempfilepath for moving the temporary file to the expected csv

Usage of the Writer
```php
// First instanciate a standard writer without targetpath, filename and not appending records
$writer = new Writer();
// Change the Strategy
$writer->getStrategy()
    ->setDelimiter("\t")
    ->setEncoding(CharsetEncoding::ISO_8859_1) // the encoding of the file
    ->setSkipEmptyRecords(true);

$header = [
    'column1',
    'column2',
    'column3',
];

$writer->writeHeader($header); // will return true on sucess
// Than write all Records (multidimensional array which contains each row as separate array)
$records = [
    [
        'foo' => 1,
        'bar' => 'bar',
        'baz' => 1.56,
    ],
    [
        'foo' => 'Foo Bar Man',
        'bar' => 34.98,
        'baz' => 1000,
    ],
];
$writer->writeRecords($records);
// will return true if successful otherwise false
/* content of temp file are tab separated records with header:
column1 column2 column3
1   bar 1.56
"Foo Bar Man"   34.98   1000
*/
```

## Benefit 
use the Reader and Writer as a composition

```php
$reader = new Reader($file);
$reader->getStrategy()
    ->setDelimiter("\t")
    ->setEncoding(CharsetEncoding::ISO_8859_1) // the encoding of the file
    ->setSkipEmptyRecords(true);

$writer = new Writer($targetPath, 'file_without_empty_lines.csv');
$writer->setStrategy($reader->getStrategy());
$writer->writeHeader($reader->getHeader());
$writer->writeRecords($reader->readAllRecords());
```
### What is planned?
I will implement an Transformer which should transform the Values from string to php int, float etc and vis versa.
It should also normalize the float values with different decimal character.

If you have any questions or ideas don't hesitate to contact me.
Your are free to copy, fork or support this little project ;-)
