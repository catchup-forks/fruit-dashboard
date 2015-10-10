<?php

/* This class is responsible for table widgets data collection. */
class TableDataManager extends DataManager
{
    /**
     * getHeader
     * Returns the header from data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getHeader() {
        $data = $this->getData();
        if ( ! array_key_exists('header', $data)) {
            return array();
        }
        return $data['header'];
    }

    /**
     * getContent
     * Returns the content from data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getContent() {
        $data = $this->getData();
        if ( ! array_key_exists('content', $data)) {
            return array();
        }
        return $data['content'];
    }

    /**
     * getRow
     * Returns the specific row.
     * --------------------------------------------------
     * @param int $row
     * @return array
     * --------------------------------------------------
     */
    public function getRow($row) {
        $content = $this->getContent();
        if ( ! array_key_exists($row, $content)) {
            return array();
        }
        return $content[$row];
    }

    /**
     * findValue
     * Returns the specific row, where value can be found.
     * --------------------------------------------------
     * @param string $value
     * @param col
     * @return int
     * --------------------------------------------------
     */
    public function findValue($value, $col=null) {
        $header = $this->getHeader();
        if (array_key_exists($col, $header)) {
            $colId = $header[$col];
        }
        foreach ($this->getContent() as $i=>$entry) {
            if (isset($colId) && $entry[$colId] == $value) {
                return $i;
            }

            foreach ($entry as $iValue) {
                if ($iValue = $value) {
                    return $i;
                }
            }
        }
        return -1;
    }

    /**
     * deleteRow
     * Deletes a specific row from the dataset.
     */
    public function deleteRow($row) {
        $content = $this->getContent();
        if ( ! array_key_exists($row, $content)) {
            return;
        }
        unset($content[$row]);
        $this->saveContent($content);
    }

    /**
     * clearTable
     * Deletes all rows from the table
     */
    public function clearTable() {
        $this->saveData(array('header' => array(), 'content' => array()));
    }

    /**
     * insert
     * Inserting a row to the dataset.
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
    public function insert($data) {
        $currentData = $this->getContent();
        $entry = array();
        foreach ($this->getHeader() as $name=>$key) {
            if (array_key_exists($key, $data)) {
                $entry[$key] = $data[$key];
            } else if (array_key_exists($name, $data)) {
                $entry[$key] = $data[$name];
            } else {
                $entry[$key] = "";
            }
        }

        array_push($currentData, $entry);
        $this->saveContent($currentData);
    }

    /**
     * addCol
     * Adding a new entry to the header.
     * --------------------------------------------------
     * @param string $key
     * @param string $defaultValue
     * --------------------------------------------------
     */
    public function addCol($key, $defaultValue='') {
        $header = $this->getheader();
        if (is_null($header) || empty($header)) {
            /* Empty dataset */
            $this->saveHeader(array($key => 0));
       } else if (in_array($key, array_keys($header))) {
             return;
       } else {
            /* Adding to datasets. */
           $id =  count($header);
           $header[$key] = $id;
           $this->saveHeader($header);

           /* Adding 0 to previous values. */
           $content = array();
           foreach ($this->getContent() as $entry) {
                $newEntry = $entry;
                $newEntry[$id] = $defaultValue;
                array_push($content, $newEntry);
           }

           $this->saveContent($content);
        }
    }

    /**
     * hasCol
     * Returns whether or not the col exists.
     * --------------------------------------------------
     * @param string $col
     * @return boolean
     * --------------------------------------------------
     */
    public function hasCol($col) {
        $header = $this->getHeader();
        return array_key_exists($col, $header);
    }

    /**
     * deleteCol
     * Removes the specific col.
     * --------------------------------------------------
     * @param string $col
     * --------------------------------------------------
     */
    public function deleteCol($col) {
        $header = $this->getHeader();
        /* Saving id and deleting col from header. */
        $dataId = $header[$col];
        unset($header[$col]);

        /* Creating a new content */
        $content = $this->getContent();
        $newContent = array();
        foreach ($content as $row) {
            unset($row[$dataId]);
            array_push($newContent, $row);
        }

        $this->saveData(array('header' => $header, 'content' => $newContent));
    }


    /**
     * saveContent
     * Saving content.
     * --------------------------------------------------
     * @param array $content
     * @param boolean $commit
     * --------------------------------------------------
     */
    public function saveContent($content, $commit=TRUE) {
        $header = $this->getHeader();
        $this->data->raw_value = json_encode(array(
            'header'  => $header,
            'content' => $content
        ));

        if ($commit) {
            $this->data->save();
        }
    }

    /**
     * saveHeader
     * Saving header.
     * --------------------------------------------------
     * @param array $header
     * @param boolean $commit
     * --------------------------------------------------
     */
    public function saveHeader($header, $commit=TRUE) {
        $content = $this->getContent();
        $this->data->raw_value = json_encode(array(
            'header'  => $header,
            'content' => $content
        ));

        if ($commit) {
            $this->data->save();
        }
    }

    /**
     * saveData
     * Saving both content, and header
     * --------------------------------------------------
     * @param array $content
     * @param array $header
     * --------------------------------------------------
     */
    public function saveData($data) {
        if ( ! array_key_exists('header', $data) || ! array_key_exists('content', $data)) {
            $this->collectData();
            return;
        }
        $this->saveHeader($data['header']);
        $this->saveContent($data['content']);
    }

}