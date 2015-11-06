<?php

trait TableWidgetTrait 
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

    /**
     * deleteRow
     * Deletes a specific row from the dataset.
     */
    protected function deleteRow($row) {
        if ( ! array_key_exists($row, $this->content)) {
            return;
        }
        unset($this->content[$row]);
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

       if (in_array($key, array_keys($header))) {
             return;
       } else {
            /* Adding to header. */
           $id =  count($header);
           $header[$key] = $id;

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
     * deleteCol
     * Removes the specific col.
     * --------------------------------------------------
     * @param string $col
     * --------------------------------------------------
     */
    protected function deleteCol($col) {
        /* Saving id and deleting col from header. */
        $dataId = $header[$col];
        unset($this->header[$col]);

        /* Creating a new content. */
        $content = $this->content;
        $newContent = array();
        
        /* Unsetting keys. */
        foreach ($content as $row) {
            unset($row[$dataId]);
            array_push($newContent, $row);
        }

        $this->content = $newContent;
    }
}
