<?php

use SilverStripe\Forms\RequiredFields;

class Autolink_Validator extends RequiredFields {

    function php($data) {
        $bRet = parent::php($data);
        $identifierField = 'keyword';
        //do checking here
        if (empty($data['keyword']))
            $this->validationError('keyword','Keyword cannot be empty','required');
        if (empty($data['Url']))
            $this->validationError('Url','Url cannot be empty','required');

        $data['ID'] = $id;

            $autolink = Autolink::get()->filter($identifierField, $data[$identifierField]);
            if ($id) {
                $autolink = $autolink->exclude('ID', $id);
            }

            if ($autolink->count() > 0) {
                $this->validationError(
                    $identifierField,
                    _t(
                        'SilverStripe\\Security\\Autolink.VALIDATIONKEYWORDEXISTS',
                        'A keyword already exists with the same {identifier}',
                        array('identifier' => Autolink::singleton()->fieldLabel($identifierField))
                    ),
                    'required'
                );
                $valid = false;
            }
        return count($this->getErrors());

    }
}
