<?php
abstract class threejdb{
    private
        $con;

    protected
        $result,
        $stmt; 

    public 
        $affected_rows,
        $dberror="database error";
    /**
     * @return object|false - mysqli connection object or false on failure
     */
    protected function newDatabaseConnection(){
        
        $this->con = new mysqli(
            $GLOBALS['SERVER'],
            $GLOBALS['DBUSER'],
            $GLOBALS['DBPASS'],
            $GLOBALS['DATABASE']
        );
        if(gettype($this->con) == 'object' && $this->con->connect_errno != 0){
            $this->dberror = $this->con->connect_error;
            return false;
        }else{
            return $this->con;
        }
        return false;
    }

    
    abstract function error(string $e, string $userErr, mixed $return=false);
    /**
     * @param array $values - Array of arrays of type and reference of variable, check mysqli_stmt_bind_param function for more detail
     *
     */
    function query(string $sql, array $values=[]){
        $stmt = $this->stmt = $this->con->prepare($sql);
        if(false !== $stmt && gettype($stmt) == 'object'){
            if(!empty($values)){
                $type='';
                $param[0] = '';
                foreach($values as $k=>$v){
                    $param[] = &$v[0];
                    $type .= $v[1];
                }
                $param[0] = $type;
                call_user_func_array([$stmt,'bind_param'],$param);
            }
            return true;
        }
        $this->dberror = $this->con->error;
        return false;
    }
    function prepare(string $sql, $values=[]){
        return $this->query($sql,$values[]);
    }
    function execute()
    {
        if($this->stmt == NULL){
            $this->dberror = $this->con->error;
            return false;
        }

        $this->result = $this->stmt->execute();

        if(false == $this->result){
            $this->dberror = $this->stmt->error;
            return false;
        }

        $this->result = $this->stmt->get_result();
        $this->affected_rows =$this->stmt->affected_rows;
        return true;
    }
    /**
     * @return array returns mysqli result as an associative array
     */
    function fetch(){
        if($this->result == NULL || 'object' != gettype($this->result)){
            return [];
        }
        return $this->result->fetch_assoc();
    }
}
?>