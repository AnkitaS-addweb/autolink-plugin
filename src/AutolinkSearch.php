<?php

namespace SilverStripe\Autolink;


use SilverStripe\Assets\File;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\ORM\Connect\MySQLSchemaManager;
use SilverStripe\ORM\DataExtension;
use Exception;
use Autolink;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Control\Director;
use SilverStripe\View\ArrayData;


/**
 * Provides a simple search engine for your site based on the MySQL FULLTEXT index.
 * Adds the {@link FulltextSearchable} extension to data classes,
 * as well as the {@link ContentControllerSearchExtension} to {@link ContentController}
 * (if the 'cms' module is available as well).
 * (this means you can use $SearchForm in your template without changing your own implementation).
 *
 * CAUTION: Will make all files in your /assets folder searchable by file name
 * unless "File" is excluded from FulltextSearchable::enable().
 *
 * @see http://doc.silverstripe.org/framework/en/tutorials/4-site-search
 */
class AutolinkSearch extends DataExtension
{

    /**
     * Comma-separated list of database column names
     * that can be searched on. Used for generation of the database index defintions.
     *
     * @var string
     */
    protected $searchFields;

    /**
     * @var array List of class names
     */
    protected static $searchable_classes;

    /**
     * Enable the default configuration of MySQL full-text searching on the given data classes.
     * It can be used to limit the searched classes, but not to add your own classes.
     * For this purpose, please use {@link Object::add_extension()} directly:
     * <code>
     * MyObject::add_extension("FulltextSearchable('MySearchableField,MyOtherField')");
     * </code>
     *
     * Caution: This is a wrapper method that should only be used in _config.php,
     * and only be called once in your code.
     *
     * @param array $searchableClasses The extension will be applied to all DataObject subclasses
     *  listed here. Default: {@link SiteTree} and {@link File}.
     * @throws Exception
     */
    public static function enable($searchableClasses = [SiteTree::class, File::class])
    {
        $listautolink = DB::query('SELECT * FROM "Autolink"');

        /*$arrayData = new ArrayData([
            'Content' => 'Content'
        ]);

        echo $arrayData->renderWith(array('Page'));*/

    }

    /**
     * @param array|string $searchFields Comma-separated list (or array) of database column names
     *  that can be searched on. Used for generation of the database index defintions.
     */
    public function __construct($searchFields = array())
    {
        parent::__construct();
        if (is_array($searchFields)) {
            $this->searchFields = $searchFields;
        } else {
            $this->searchFields = explode(',', $searchFields);
            foreach ($this->searchFields as &$field) {
                $field = trim($field);
            }
        }
    }

    public static function get_extra_config($class, $extensionClass, $args)
    {
        return array(
            'indexes' => array(
                'SearchFields' => array(
                    'type' => 'fulltext',
                    'name' => 'SearchFields',
                    'columns' => $args,
                )
            )
        );
    }

    /**
     * Shows all classes that had the {@link FulltextSearchable} extension applied through {@link enable()}.
     *
     * @return array
     */
    public static function get_searchable_classes()
    {
        return self::$searchable_classes;
    }
}
