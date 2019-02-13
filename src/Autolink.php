<?php

use SilverStripe\ORM\DataObject;

class Autolink extends DataObject 
{

    private static $db = [
        'keyword' => 'Varchar',
        'Url' => 'Varchar',
    ];


    private static $searchable_fields = [
      'keyword',
	  'Url'
	];

	private static $summary_fields = [
	  'keyword',
	  'Url'
	];

    
    function getCMSValidator() {
        return new Autolink_Validator();
    }
}
?>