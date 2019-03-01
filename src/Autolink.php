<?php

namespace SilverStripe\Autolink;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\TabSet;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Queries\SQLUpdate;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\CMS\Controllers\CMSMain;

class Autolink extends DataObject
{

    private static $db = [
        'Key' => 'Varchar',
        'Url' => 'Varchar',
        'PageType' => 'Varchar'
    ];

    private static $searchable_fields = [
      'Key',
      'Url'
    ];

    private static $summary_fields = [
      'Key',
      'Url'
    ];


    public function getCMSfields()
    {


        $pageTypeList = array();
        $resultColumn = DB::query("SELECT DISTINCT(ClassName) as ClassName FROM SiteTree");
        foreach($resultColumn as $resultColumnKey => $resultColumnVal)
        {
            $pagetype = explode("\\",$resultColumnVal['ClassName']);

           /* print_r(SiteTree::get()->map(['ClassName' => $value['PageType']]));
                exit;*/
            if(sizeof($pagetype)>1)
            {
                $pageTypeList[$resultColumnVal['ClassName']] = $pagetype[sizeof($pagetype)-1];
            }
            else
            {
                $pageTypeList[$resultColumnVal['ClassName']] = $resultColumnVal['ClassName'];    
            }
            
        }
        


        $fields = FieldList::create(TabSet::create('Root'));
        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Key'),
            TextField::create('Url'),
           /* TreeDropdownField::create(
                'PageType',
                _t('Linkable.PageType', 'PageType'),
                //BlogPost::class
                SiteTree::class
            ),*/
            DropdownField::create( 'PageType', 'PageType', $pageTypeList )
        ]);

        return $fields;
    }

    function getCMSValidator() {
        return new Autolink_Validator();
    }

    function onAfterWrite() {
        
        $ListAutolinks = DB::query('SELECT * FROM SilverStripe_Autolink_Autolink');
        foreach ($ListAutolinks as $key => $value) 
        {

            $getContent = DB::query("SELECT * FROM SiteTree where ClassName ='".$value['PageType']."'");
            foreach($getContent as $getKey => $getValue)
            {
                $replaceString = "<a href='".$value['Url']."'>".$value['Key']."</a>";
                $replacedContent = str_replace($value['Key'],$replaceString,$getValue['Content']);

                //SiteTree
                $updateSiteTree = SQLUpdate::create('"SiteTree"')->addWhere(array('ClassName' => $value['PageType'], 'ID'=>$getValue['ID']));
                $updateSiteTree->assign('"Content"', $replacedContent);
                $updateSiteTree->execute();
                //SiteTree_Live
                $updateSiteTree_Live = SQLUpdate::create('"SiteTree_Live"')->addWhere(array('ClassName' => $value['PageType'], 'ID'=>$getValue['ID']));
                $updateSiteTree_Live->assign('"Content"', $replacedContent);
                $updateSiteTree_Live->execute();

                //SiteTree_version
                $SiteTree_version = SQLUpdate::create('"SiteTree_Versions"')->addWhere(array('ClassName' => $value['PageType'], 'ID'=>$getValue['ID']));
                $SiteTree_version->assign('"Content"', $replacedContent);
                $SiteTree_version->execute();
            }
        }
    }
}

?>
