<?php

use SilverStripe\ORM\DataObject;
use SilverStripe\CMS\Model\SiteTree;

class Autolink extends DataObject 
{

    private static $db = [
        'keyword' => 'Varchar',
        'Url' => 'Varchar',
        'ContentType' => 'Varchar',
        'PageRegion' => 'Int',
    ];


    private static $searchable_fields = [
      'keyword',
	  'Url'
	];

	private static $summary_fields = [
	  'keyword',
	  'Url'
	];

    private static $has_many = array (
        'PageRegion' => 'PageRegion',
      );

 
    function getCMSValidator() {
        return new Autolink_Validator();
    }
}
?>