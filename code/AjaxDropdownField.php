<?php

class AjaxDropdownField extends DropdownField
{

    public static $allowed_actions = [
        'suggest',
    ];

    protected $suggestionCallback;
    protected $minLength  = 1;
    protected $pageLength = 150;
    protected $IDColumn;
    protected $textColumn;

    public function __construct($name, $title = null, $idColumn = 'ID', $textColumn = 'Title', $value = null)
    {
        parent::__construct($name, $title, null, $value);

        $this->setIDColumn($idColumn);
        $this->setTextColumn($textColumn);
        $this->addExtraClass('ajax-dropdown-field select2 no-chzn');
    }

    public function getSource()
    {
        if (!$this->suggestionCallback) {
            throw new Exception('Suggestion callback not defined');
        }

        return $this->suggestionCallback;
    }

    public function setMinLength($length)
    {
        $this->minLength = $length;
        return $this;
    }

    public function getMinLength()
    {
        return $this->minLength;
    }

    public function getPageLength()
    {
        return $this->pageLength;
    }

    public function setPageLength($length)
    {
        $this->pageLength = $length;
        return $this;
    }

    public function getIDColumn()
    {
        return $this->IDColumn;
    }

    public function setIDColumn($column)
    {
        $this->IDColumn = $column;
        return $this;
    }

    public function getTextColumn()
    {
        return $this->IDColumn;
    }

    public function setTextColumn($column)
    {
        $this->IDColumn = $column;
        return $this;
    }

    /**
     * @param Closure $source
     * @return $this
     */
    public function setSource($source)
    {
        if (is_callable($source)) {
            $this->suggestionCallback = $source;
        }

        return $this;

    }

    public function getAttributes()
    {
        $attributes = parent::getAttributes();
        if ($this->getHasEmptyDefault()) {
            $attributes['data-placeholder'] = $this->getEmptyString();
        }

        if (!$this->Required()) {
            $attributes['data-allow-clear'] = 'true';
        }

        $attributes['data-suggest-min-length']  = $this->getMinLength();
        $attributes['data-suggest-page-length'] = $this->getPageLength();
        $attributes['data-suggest-url']         = Controller::join_links($this->Link(), 'suggest');

        return $attributes;
    }

    public function Field($properties = array())
    {
        Requirements::css('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css');
        Requirements::javascript('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.min.js');
        Requirements::javascript(AJAX_DROPDOWN_DIR . "/javascript/ajax-dropdown-field.js");
        Requirements::css(AJAX_DROPDOWN_DIR . "/css/ajax-dropdown-field.css");

        return parent::Field($properties);
    }

    public function suggest(SS_HTTPRequest $request)
    {
        $searchTerm = $request->getVar('term');
        $pageNumber = $request->getVar('page') ?: 1;

        $pageSize           = $this->getPageLength();
        $suggestionFunction = $this->getSource();
        $suggestionResults  = [];
        $suggestionFunction($searchTerm)
            ->limit($pageSize, (($pageNumber - 1) * $pageSize))
            ->each(function ($item) use (&$suggestionResults) {
                $suggestionResults[] = [
                    'id'   => $item->{$this->getIDColumn()},
                    'text' => $item->{$this->getTextColumn()}
                ];
            });


        $response = new SS_HTTPResponse();
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode(['items' => $suggestionResults]));

        return $response;
    }

}