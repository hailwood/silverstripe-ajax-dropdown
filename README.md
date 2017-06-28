# silverstripe-ajax-dropdown
An silverstripe dropdown which loads results via ajax to handle large datasets

## Example usage (I'll document this better later)

```php
// Define a callback to load the results, don't worry about pagination of limiting here
$dataSource = function ($term = null) {
    return SomeDataObject::get()->filter(['Title:PartialMatch' => $term]);
};

// Here ID and Title are optional as they are the defaults, they are the names of the columns to
// pull the ID and Text of the dropdown option from
$field = AjaxDropdownField::create('LabelID', 'Label', 'ID', 'Title');

// This is the only required function call, of course feel free to just chain this on after ::create(...)
$field->setSource($dataSource);

// These methods are totally optional, I'm going to call them with their defaults below
$field->setIDColumn('ID');      // Set the name of the column to pull the dropdown options ID from
$field->setTextColumn('Title'); // Set the name of the column to pull the dropdown options text from
$field->setMinLength(1);        // Set the number of characters that must be entered before a search will be performed
$field->setPageLength(150);     // Set the number of results that will be returned on each search or "load more"
// Each of the above methods also has an associated "getter" e.g. $field->getPageLength();
```

## Other Methods
This field extends `DropdownField` so any methods you can call on there e.g. `setEmptyString()` will also work here.

## Other Modules
This field should work with most other modules, I have explicitly tested this with [sheadawson/silverstripe-quickaddnew](https://github.com/sheadawson/silverstripe-quickaddnew)
