<?php
require_once "libdb.php";
require_once "libcity.php";

class Foodstuff extends db{
  private $_city;
  /* protected propaties */
  protected $_bigcategory;
  protected $_middlecategory;
  /* end of protected propaties*/

  /* constructor */
  public function __construct( $__host, $__user, $__passwd ){
    $query = "SELECT * from ";

    $this->_bigcategory = array();
    $this->_middlecategory = array();
    $this->_city = new City( $__host, $__user, $__passwd );

    parent::__construct( $__host, $__user, $__passwd );
    // get category name
    $result = $this->_db_throw_query( "Foodstuff", $query."Foodstuff.Big_Category_List;" );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }
    while( $records = mysqli_fetch_assoc( $result ) ){
      $this->_bigcategory[$records['Big_Category_ID']] = $records['Big_Category_Name'];
    }
    $result = $this->_db_throw_query( "Foodstuff", $query."Foodstuff.Middle_Category_List;" );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }
    while( $records = mysqli_fetch_assoc( $result ) ){
      $big = $records['Middle_Category_ID'][0];
      $middle = $records['Middle_Category_ID'][1];
      $this->_middlecategory[$big][$middle] = $records['Middle_Category_Name'];
    }
  }
  /* end of constructor */

  /* protected method */
  protected function _GetList( $_query, $_db ){
    $return = array();
    $result = $this->_db_throw_query( $_db, $_query );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }

    for( $i = 0; ( $record = mysqli_fetch_assoc( $result ) ) != NULL; $i++ ){
      $return[$i]["ID"] = $record[$_db."_ID"];
      $return[$i]["Name"] = $record[$_db."_Name"];
    }
    return $return;
  }
  /* end of protected method */

  /* public methods */
  /* method that get foodstuff name from id */
  public function GetFoodstuffNamefromID( $_ID ){
    $query = "SELECT Foodstuff_Name from Foodstuff.Foodstuff_List where Foodstuff_ID = '";

    $result = $this->_db_throw_query( "Foodstuff", $query.$_ID."';" );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }

    $records = mysqli_fetch_assoc( $result );
    return $records['Foodstuff_Name'];
  }
  /* method that get id from foodstuff name */
  public function GetIDfromFoodstuffName( $_FoodstuffName ){
    $query = "SELECT Foodstuff_ID from Foodstuff.Foodstuff_List where Foodstuff_Name = '";

    $result = $this->_db_throw_query( "Foodstuff", $query.$_FoodstuffName."';" );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }

    $records = mysqli_fetch_assoc( $result );
    return $records['Foodstuff_ID'];
  }
  /* method that get big-category name from id */
  public function GetBigCategoryfromID( $_ID ){
    return $this->_bigcategory[$_ID[0]];
  }
  /* method that get middle-category name from id */
  public function GetMiddleCategoryfromID( $_ID ){
    return $this->_middlecategory[$_ID[0]][$_ID[1]];
  }
  /* method that get category name from id */
  public function GetCategoryfromID( $_ID ){
    $result = array( 0 => $this->GetBigCategoryfromID( $_ID ),
      1 => $this->GetBigCategoryfromID( $_ID ) );
    return $result;
  }
  /* method that get id from category name */
  public function GetCategoryIDfromName( $_CategoryName ){
    for( $i = 0; $i < 5; $i++ ){
      if( !strcmp( $this->_bigcategory[$i], $_CategoryName ) ){
        $j = NULL;
        break;
      }
      for( $j = 0; ; $j++ ){
        if( !strcmp( $this->_middlecategory[$i][$j], $_CategoryName ) )
          goto outside;
      }
    }
    outside:

    $result = array( 0 => $i, 1 => $j );
    return $result;
  }
  /* method get foodstuff list */
  public function GetFoodstuffList( $_CategoryID, $_ProcessID, $_AllergyID ){
    $query = "SELECT * FROM Foodstuff.Foodstuff_List where Foodstuff_ID like '";

    if( $_CategoryID == 0 )
      $query = $query."__";
    else if( $_CategoryID < 9 )
      $query = $query.strval( $_CategoryID )."_";
    else
      $query = $query.strval( $_CategoryID );
    if( $_ProcessID != 0 )
      $query = $query.strval( $_ProcessID );
    else
      $query = $query."_";
    if( $_AllergyID != 0 )
      $query = $query.strval( $_AllergyID );
    else
      $query = $query."_";
    $query = $query."%';";
    
    return $this->_GetList( $query, "Foodstuff" );
  }
  /* method that get price of foodstuff */
  public function GetFoodstuffPrice( $_UserID, $_FoodstuffID ){
    $query = "SELECT Price, Mount from U".$_UserID." where ID = '".$_FoodstuffID."' Order by Day DESC;";

    $result = $this->_db_throw_query( "Users_Geo", $query );
    $record = mysqli_fetch_assoc( $result );
    while( mysqli_next_result( $this->_connection ) ){
      mysqli_store_result( $this->_connection );
    }
    if( $record != NULL ){
      $price = $record["Price"] / $record["Mount"];
    } else{
      $list = $_FoodstuffID;
      $this->_city->SerchPrice( 2.0, $_UserID, $list );
      $price = $list;
    }
    
    return $price;
  }
  /***************************************************
    $User_ID    ユーザーID
    $Foodstuff_ID 食材ID
    $Price      食材価格
    $Amount     食材料
    $Date       購入日時 NULLなら登録時
  ***************************************************/
  public function RegPrice($User_ID,$Foodstuff_ID,$Price,$Amount,$Date){
    if($Date == NULL )
      $Date = date('Y/m/t ');
    $query = sprintf( "insert into U%06s VALUES('%s','%s','%s','%s')", $User_ID, $Foodstuff_ID, $Price, $Amount, $Date );
    $result = $this->_db_throw_query( "Users_Geo", $query );
    if( !$result ){
      print( "Quely Failed.\n".mysqli_error( $this->_connection ) );
      return NULL;
    }
  }
  /* end of public methods */

  /* destructor */
  public function __destruct(){
    parent::__destruct();
  }
  /* end of destructor */
}
?>