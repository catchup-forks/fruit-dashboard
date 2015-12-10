<?php

abstract class TableWidget extends DataWidget
{
    /**
     * The header of the table.
     *
     * @var array
     */
    protected $header = array();

    /**
     * The content of the table.
     *
     * @var array
     */
    protected $content = array();

    /* -- Settings -- */
    private static $tableSettings = array(
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The name of the widget.'
        ),
    );

    /* Create these functions on inheritance. */
    abstract protected function buildHeader();
    abstract protected function buildContent();

    /**
     * getData
     * Rebuild the table.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getData(array $postData=array()) {
        return $this->buildTable();
    }

    /**
     * buildTable
     * Build the table data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    protected function buildTable()
    {
        /* Building the header. */
        $this->buildHeader();

        /* Building the content. */
        $this->buildContent();
    
        /* Transforming to HTML. */
        return array(
            'header'  => $this->headerToHTML(),
            'content' => $this->contentToHTML()
        );
    }

    /**
     * headerToHTML
     * Transforming the header to HTML format.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function headerToHTML()
    {
        $html = "<thead>\n";

        foreach ($this->header as $header) {
            $html .= "\t<th>$header</th>\n";
        }

        $html .= "</thead>";

        return $html;
    }

    /**
     * contentToHTML
     * Transforming the content to HTML format.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function contentToHTML()
    {
        $html = "<tbody>\n";

        foreach ($this->content as $content) {
            $html .= "\t<tr>\n";
            foreach ($content as $cell) {
                $html .= "\t\t<td>$cell</td>\n";
            }
            $html .= "\t</tr>\n";
        }

        $html .= "</tbody>";

        return $html;
    }

    /**
     * clearTable
     * Deletes all rows from the table
     */
    protected function clearTable() {
        $this->content = array();
        $this->header = array();
    }

    /**
     * insert
     * Inserting a row to the dataset.
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
    protected function insert(array $data) {
        $entry = array();

        foreach ($this->header as $name=>$key) {
            if (array_key_exists($key, $data)) {
                $entry[$key] = $data[$key];
            } else if (array_key_exists($name, $data)) {
                $entry[$key] = $data[$name];
            } else {
                $entry[$key] = "";
            }
        }

        array_push($this->content, $entry);
    }

    /**
     * addCol
     * Adding a new entry to the header.
     * --------------------------------------------------
     * @param string $key
     * @param string $defaultValue
     * --------------------------------------------------
     */
    protected function addCol($key, $defaultValue='') {
        $header = $this->header;

        if (in_array($key, array_values($header))) {
             return;
        } else {
            /* Adding to header. */
            $id =  count($header);
            $header[$id] = $key;

            /* Saving header. */
            $this->header = $header;

            /* Adding empty values to previous entries. */
            $content = array();
            foreach ($this->content as $entry) {
                 $newEntry = $entry;
                 $newEntry[$id] = $defaultValue;
                 array_push($content, $newEntry);
            }

            $this->content = $content;
        }
    }

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public static function getSettingsFields()
    {
        return array_merge(
            parent::getSettingsFields(),
            array('Table settings' => self::$tableSettings)
        );
    }

    /**
     * getTemplateData
     * Return the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'name' => $this->getName(),
            'data' => array(SiteConstants::LAYOUT_TABLE => $this->buildTable()),
            'possibleLayouts' => array(SiteConstants::LAYOUT_TABLE)
        ));
    }

    /**
     * getTemplateMeta
     * Return data for the gridster init template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getTemplateMeta()
    {
        $meta = parent::getTemplateMeta();

        $meta['layout'] = 'table';
        $meta['general']['name'] = $this->getName();
    
        return $meta;
    }

    /**
     * getName
     * Return the name of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    protected function getName() {
        $name = '';
        if ($this instanceof iServiceWidget && $this->hasValidCriteria()) {
            $name = $this->getServiceSpecificName();
        }
        $name .= ' ' . $this->getSettings()['name'];
        return $name;
    }

    /**
     * saveSettings
     * Collecting new data on change.
     * --------------------------------------------------
     * @param array $inputSettings
     * @param boolean $commit
     * --------------------------------------------------
    */
    public function saveSettings(array $inputSettings, $commit=true) {
        $changedFields = parent::saveSettings($inputSettings, $commit);
        if ($this->getSettings()['name'] == '') {
            $this->saveSettings(array('name' => $this->getDescriptor()->name), $commit);
        }
        return $changedFields;
    }
}

?>
