<?php
require_once "libdb.php";
require_once "libfoodstuff.php";

class Menu extends Foodstuff{
  /* public propaties */
  public $_ganre;
  public $_method;
  public $_kind;
  /* end of public propaties*/

  /* constructor */
  public function __construct( $__host, $__user, $__passwd ){
    $query = "SELECT * from ";

    $this->_ganre = array();
    $this->_method = array();
    $this->_kind = array(
                    "main" => 0,
                    "dish" => 1,
                    "sub"  => 2,
                    "soup" => 3, );

    // initialize Foodstuff
    parent::__construct( $__host, $__user, $__passwd );
    // get ganre name
    $result = $this->_db_throw_query( "Menu", $query."Menu.Ganre_List;" );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }
    while( $records = mysqli_fetch_assoc( $result ) ){
      $this->_ganre[$records['Ganre_ID']] = $records['Ganre_Name'];
    }
    // get method name
    $result = $this->_db_throw_query( "Menu", $query."Menu.Method_List;" );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }
    while( $records = mysqli_fetch_assoc( $result ) ){
      $this->_ganre[$records['Method_ID']] = $records['Method_Name'];
    }
  }
  /* end of constructor */

  /* public methods */
  /* method that get menu name from id */
  public function GetMenuNamefromID( $_ID ){
    $query = "SELECT Menu_Name from Menu.Menu_List where Menu_ID = '";

    $result = $this->_db_throw_query( "Menu", $query.$_ID."';" );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }

    $records = mysqli_fetch_assoc( $result );
    return $records['Menu_Name'];
  }
  /* method that get id from menu name */
  public function GetIDfromMenuName( $_MenuName ){
    $query = "SELECT Menu_ID from Menu.Menu_List where Menu_Name = '";

    $result = $this->_db_throw_query( "Menu", $query.$_MenuName."';" );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }

    $records = mysqli_fetch_assoc( $result );
    return $records['Menu_ID'];
  }
  /* method that get ganre name from id */
  public function GetGanrefromID( $_ID ){
    return $this->_ganre[$_ID[2]];
  }
  /**/
  public function GetMethodfromID( $_ID ){
    return $this->_method[$_ID[3]];
  }
  /* method that get id from ganre name */
  public function GetGanreIDfromName( $_GanreName ){
    for( $i = 0; $i < 5; $i++ ){
      if( !strcmp( $this->_ganre[$i], $_GanreName ) )
        break;
    }

    return $i;
  }
  /* method that get id from method name */
  public function GetMethodIDfromName( $_MethodName ){
    for( $i = 0; $i < 5; $i++ ){
      if( !strcmp( $this->_method[$i], $_MethodName ) )
        break;
    }

    return $i;
  }
  /* method get foodstuff list*/
  public function GetFoodstuffListfromID( $_MenuID ){
    $return = array();
    $query = "SELECT * from M".$_MenuID.";";
    
    $result = $this->_db_throw_query( "Menu", $query );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }
    for( $i = 0; ( $record = mysqli_fetch_assoc( $result ) ) != NULL; $i++ ){
      $return[$i]["Name"] = $this->GetFoodstuffNamefromID( $record["Foodstuff_ID"] );
      $return[$i]["Amount"] = $record["Amount"];
      $return[$i]["Unit"] = $record["Amount"];
    }

    return $return;
  }
  /* method get menu list */
  public function GetMenuList( $_CategoryID, $_GanreID, $_MethodID, $_KindID ){
    $query = "SELECT * FROM Menu.Menu_List where Menu_ID like '";

    if( $_CategoryID == 0 )
      $query = $query."__";
    else if( $_CategoryID < 9 )
      $query = $query.strval( $_CategoryID )."_";
    else
      $query = $query.strval( $_CategoryID );
    if( $_GanreID != 0 )
      $query = $query.strval( $_GanreID );
    else
      $query = $query."_";
    if( $_MethodID != 0 )
      $query = $query.strval( $_MethodID );
    else
      $query = $query."_";
    if( strcmp( $_KindID, "0000" ) == 0 )
      $query = $query."____";
    else
      $query = $query.$_KindID;
    $query = $query."%';";
    
    return $this->_GetList( $query, "Menu" );
  }
  /* method that get price of menu */
  public function GetMenuPrice( $_UserID, $_MenuID ){
    $sum = 0;
    $price = array();
    $query = "SELECT * from M".$_MenuID.";";

    $result = _db_throw_query( "Menu", $query );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }
    while( ( $record = mysqli_fetch_assoc( $result ) ) != NULL ){
      $price = $this->GetFoodstuffPrice( $_UserID, $record["Foodstuff_ID"] );
      $sum += ($price[0]*$record["Amount"]);
    }

    return $sum;
  }
  /* end of public method */

  /* destructor */
  public function __destruct(){
    parent::__destruct();
  }
  /* end of destructor */
}
?>
