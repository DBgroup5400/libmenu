<?php
class db{
  /* protected propaties */
  protected $_connection;
  protected $_host;
  protected $_user;
  /* end of protected propaties*/

  /* constructor */
  public function __construct( $__host, $__user, $__passwd ){
    $this->_host = $__host;
    $this->_user = $__user;

    $this->_db_connect( $__passwd );
    if( !$this->_connection ){
      return NULL;
    }
  }
  /* end of constructer */

  /* method that connect database */
  public function _db_connect( $_passwd ){
    $this->_connection = mysqli_connect( $this->_host, $this->_user, $_passwd );
    if( !$this->_connection )
      print( "Connection Failed.\n".mysqli_error( $this->_connection ) );
  }
  /* method that select database */
  protected function _db_select( $_name ){
    $dbselect = mysqli_select_db( $this->_connection, $_name );
    if( !$dbselect ){
      print( "Select Failed.\n".mysqli_error( $this->_connection ) );
      return $dbselect;
    }
    $cherset = mysqli_query( $this->_connection, "SET NAMES utf8" );
    if( !$cherset ){
      print( "Quely Failed..\n".mysqli_error( $this->_connection ) );
      return $cherset;
    }
    return $dbselect;
  }
  /* method that throw query to detabase */
  protected function _db_throw_query( $_db_name, $_query ){
    $result = mysqli_query( $this->_connection, "SELECT database();" );
    $record = mysqli_fetch_assoc( $result );
    if( $record['database()'] != $_db_name ){
      $flag = $this->_db_select( $_db_name, $this->_connection );
      if( !$flag )
        return NULL;
    }
    $result = mysqli_query( $this->_connection, $_query );
    return $result;
  }
  /* method that disconnect database */
  public function _db_close(){
    $flag = mysqli_close( $this->_connection );
    if( !$flag )
      print( "Close Failed\n".mysqli_error( $this->_connection ) );
    return $flag;
  }
  /* end of public method */

  /* destructor */
  public function __destruct(){
    $this->_db_close();
  }
  /* end of destructor */
}
?>