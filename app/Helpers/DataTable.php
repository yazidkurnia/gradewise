<?php

/**
 * DataTable Library
 * Reusable DataTable dengan server-side processing
 * 
 * @author Yazid
 * @version 1.0
 */
class DataTableHelper
{
    private $data = [];
    private $columns = [];
    private $searchableColumns = [];
    private $orderableColumns = [];
    private $request = [];
    private $totalRecords = 0;
    private $filteredRecords = 0;
    private $primaryKey = 'id';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->request = $_REQUEST;
    }
    
    /**
     * Set data source
     * 
     * @param array $data Array of data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        $this->totalRecords = count($data);
        return $this;
    }
    
    /**
     * Set kolom yang akan ditampilkan
     * 
     * @param array $columns Format: ['key' => 'column_key', 'label' => 'Column Label', 'searchable' => true, 'orderable' => true]
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
        
        // Set searchable dan orderable columns
        foreach ($columns as $index => $column) {
            if (isset($column['searchable']) && $column['searchable']) {
                $this->searchableColumns[] = $column['key'];
            }
            if (isset($column['orderable']) && $column['orderable']) {
                $this->orderableColumns[$index] = $column['key'];
            }
        }
        
        return $this;
    }
    
    /**
     * Set primary key
     * 
     * @param string $key Primary key column name
     * @return $this
     */
    public function setPrimaryKey($key)
    {
        $this->primaryKey = $key;
        return $this;
    }
    
    /**
     * Apply search filter
     * 
     * @param array $data Data to filter
     * @return array Filtered data
     */
    private function applySearch($data)
    {
        if (empty($this->request['search']['value'])) {
            return $data;
        }
        
        $searchValue = strtolower($this->request['search']['value']);
        
        return array_filter($data, function($row) use ($searchValue) {
            foreach ($this->searchableColumns as $column) {
                if (isset($row[$column])) {
                    if (stripos($row[$column], $searchValue) !== false) {
                        return true;
                    }
                }
            }
            return false;
        });
    }
    
    /**
     * Apply ordering
     * 
     * @param array $data Data to order
     * @return array Ordered data
     */
    private function applyOrder($data)
    {
        if (empty($this->request['order'])) {
            return $data;
        }
        
        $orderColumnIndex = $this->request['order'][0]['column'];
        $orderDir = $this->request['order'][0]['dir'];
        
        if (!isset($this->orderableColumns[$orderColumnIndex])) {
            return $data;
        }
        
        $orderColumn = $this->orderableColumns[$orderColumnIndex];
        
        usort($data, function($a, $b) use ($orderColumn, $orderDir) {
            $valA = isset($a[$orderColumn]) ? $a[$orderColumn] : '';
            $valB = isset($b[$orderColumn]) ? $b[$orderColumn] : '';
            
            if ($orderDir === 'asc') {
                return strcmp($valA, $valB);
            } else {
                return strcmp($valB, $valA);
            }
        });
        
        return $data;
    }
    
    /**
     * Apply pagination
     * 
     * @param array $data Data to paginate
     * @return array Paginated data
     */
    private function applyPagination($data)
    {
        $start = isset($this->request['start']) ? (int)$this->request['start'] : 0;
        $length = isset($this->request['length']) ? (int)$this->request['length'] : 10;
        
        if ($length === -1) {
            return $data;
        }
        
        return array_slice($data, $start, $length);
    }
    
    /**
     * Format row data
     * 
     * @param array $row Row data
     * @return array Formatted row
     */
    private function formatRow($row)
    {
        $formatted = [];
        
        foreach ($this->columns as $column) {
            $key = $column['key'];
            
            // Jika ada render callback
            if (isset($column['render']) && is_callable($column['render'])) {
                $formatted[$key] = call_user_func($column['render'], $row, $key);
            } else {
                $formatted[$key] = isset($row[$key]) ? $row[$key] : '';
            }
        }
        
        // Tambahkan primary key untuk action buttons
        $formatted['DT_RowId'] = isset($row[$this->primaryKey]) ? $row[$this->primaryKey] : '';
        
        return $formatted;
    }
    
    /**
     * Generate response untuk DataTables
     * 
     * @return array DataTables response
     */
    public function generate()
    {
        // Apply search
        $filteredData = $this->applySearch($this->data);
        $this->filteredRecords = count($filteredData);
        
        // Apply order
        $filteredData = $this->applyOrder($filteredData);
        
        // Apply pagination
        $paginatedData = $this->applyPagination($filteredData);
        
        // Format data
        $formattedData = [];
        foreach ($paginatedData as $row) {
            $formattedData[] = $this->formatRow($row);
        }
        
        return [
            'draw' => isset($this->request['draw']) ? (int)$this->request['draw'] : 0,
            'recordsTotal' => $this->totalRecords,
            'recordsFiltered' => $this->filteredRecords,
            'data' => $formattedData
        ];
    }
    
    /**
     * Output JSON response
     */
    public function output()
    {
        header('Content-Type: application/json');
        echo json_encode($this->generate());
    }
}