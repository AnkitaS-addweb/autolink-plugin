<?php 

use SilverStripe\Forms\FieldList;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\DropdownField;

class AutolinkAdmin extends ModelAdmin 
{

    private static $managed_models = [
        'Autolink',
    ];

    private static $url_segment = 'autolink';
    private static $menu_title = 'Autolink';
    private static $menu_icon_class = 'font-icon-link';

    public function getSearchContext() 
    {
        $context = parent::getSearchContext();
        return $context;
    }

    public function getList() 
    {
        $list = parent::getList();  
        return $list;
    }

}
