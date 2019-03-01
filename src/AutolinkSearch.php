<?php

namespace SilverStripe\Autolink;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Queries\SQLUpdate;
use SilverStripe\CMS\Model\SiteTree;

class AutolinkSearch extends DataExtension
{
    public static function enable($searchableClasses = [SiteTree::class, File::class])
    {
        $ListAutolinks = DB::query('SELECT * FROM SilverStripe_Autolink_Autolink');
        foreach ($ListAutolinks as $key => $value) 
        {
            $getContent = DB::query("SELECT * FROM SiteTree where ClassName = '".$value['PageType']."'");
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
