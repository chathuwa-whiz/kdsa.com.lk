<?php

require_once 'connection.php';


class ModelSales{
	/*=============================================
	SHOWING SALES
	=============================================*/
	/* --LOG ON TO codeastro.com FOR MORE PROJECTS-- */

	static public function mdlShowSales($table, $item, $value){

		if($item != null){

			$stmt = Connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item ORDER BY id ASC");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Connection::connect()->prepare("SELECT * FROM $table ORDER BY id ASC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	REGISTERING SALE
	=============================================*/
	/* --LOG ON TO codeastro.com FOR MORE PROJECTS-- */
	static public function mdlAddSale($table, $data) {
		try {
			$conn = Connection::connect();
			$conn->beginTransaction();
	
			$stmt = $conn->prepare("INSERT INTO $table(code, idCustomer, idSeller, products, totalPrice, netItemsPrice, discount, discountPercentage, paymentMethod, cashin, balance) 
								   VALUES (:code, :idCustomer, :idSeller, :products, :totalPrice, :netItemsPrice, :discount, :discountPercentage, :paymentMethod, :cashin, :balance)");
	
			// Bind parameters with type checking
			try {
				$stmt->bindParam(":code", $data["code"], PDO::PARAM_INT);
				$stmt->bindParam(":idCustomer", $data["idCustomer"], PDO::PARAM_INT);
				$stmt->bindParam(":idSeller", $data["idSeller"], PDO::PARAM_INT);
				$stmt->bindParam(":products", $data["products"], PDO::PARAM_STR);
				$stmt->bindParam(":discount", $data["discount"], PDO::PARAM_STR);
				$stmt->bindParam(":discountPercentage", $data["discountPercentage"], PDO::PARAM_STR);
				$stmt->bindParam(":netItemsPrice", $data["netItemsPrice"], PDO::PARAM_STR);
				$stmt->bindParam(":totalPrice", $data["totalPrice"], PDO::PARAM_STR);
				$stmt->bindParam(":paymentMethod", $data["paymentMethod"], PDO::PARAM_STR);
				
				// Validate POST variables exist
				if (!isset($_POST["newCashValue"]) || !isset($_POST["newCashChange"])) {
					throw new Exception("Cash value or change amount is missing");
				}
				
				// Remove commas and format cash values
				$cashIn = str_replace(',', '', $_POST["newCashValue"]);
				$cashChange = str_replace(',', '', $_POST["newCashChange"]);
				
				// Validate if the values are numeric after formatting
				if (!is_numeric($cashChange) || !is_numeric($cashIn)) {
					throw new Exception("Invalid cash amount format");
				}
				
				$stmt->bindParam(":cashin", $cashIn, PDO::PARAM_STR);
				$stmt->bindParam(":balance", $cashChange, PDO::PARAM_STR);
				
				// Debug code to check values
				echo '<script>console.log("Balance value after formatting:", ' . json_encode($cashChange) . ');</script>';
			} catch (PDOException $e) {
				throw new Exception("Error binding parameters: " . $e->getMessage());
			}
	
			// Execute the statement
			if (!$stmt->execute()) {
				$errorInfo = $stmt->errorInfo();
				throw new Exception("Database error: " . $errorInfo[2]);
			}
	
			// If everything is successful, commit the transaction
			$conn->commit();
			
			return "ok";
	
		} catch (Exception $e) {
			// Roll back the transaction on error
			if (isset($conn)) {
				$conn->rollBack();
			}
	
			// Log the error
			error_log("Sale Addition Error: " . $e->getMessage());
			
			// Return detailed error for debugging
			return [
				"status" => "error",
				"message" => $e->getMessage(),
				"details" => [
					"file" => $e->getFile(),
					"line" => $e->getLine(),
					"trace" => $e->getTraceAsString()
				]
			];
	
		} finally {
			// Clean up
			if (isset($stmt)) {
				$stmt->closeCursor();
				$stmt = null;
			}
		}
	}
	/* --LOG ON TO codeastro.com FOR MORE PROJECTS-- */
	/*=============================================
	EDIT SALE
	=============================================*/
	
	static public function mdlEditSale($table, $data){

		$stmt = Connection::connect()->prepare("UPDATE $table SET idCustomer = :idCustomer, idSeller = :idSeller, products = :products, totalPrice= :totalPrice, netItemsPrice= :netItemsPrice, discount= :discount, discountPercentage= :discountPercentage, paymentMethod = :paymentMethod, cashin = :cashin, balance = :balance WHERE code = :code");

		$stmt->bindParam(":code", $data["code"], PDO::PARAM_INT);
		$stmt->bindParam(":idCustomer", $data["idCustomer"], PDO::PARAM_INT);
		$stmt->bindParam(":idSeller", $data["idSeller"], PDO::PARAM_INT);
		$stmt->bindParam(":products", $data["products"], PDO::PARAM_STR);
		$stmt->bindParam(":discount", $data["discount"], PDO::PARAM_STR);
		$stmt->bindParam(":discountPercentage", $data["discountPercentage"], PDO::PARAM_STR);
		$stmt->bindParam(":netItemsPrice", $data["netItemsPrice"], PDO::PARAM_STR);
		$stmt->bindParam(":totalPrice", $data["totalPrice"], PDO::PARAM_STR);
		$stmt->bindParam(":paymentMethod", $data["paymentMethod"], PDO::PARAM_STR);
		$stmt->bindParam(":cashin", $_POST["newCashValue"], PDO::PARAM_STR);
		$stmt->bindParam(":balance", $_POST["newCashChange"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}
	/* --LOG ON TO codeastro.com FOR MORE PROJECTS-- */
	/*=============================================
	DELETE SALE
	=============================================*/

	static public function mdlDeleteSale($table, $data){

		$stmt = Connection::connect()->prepare("DELETE FROM $table WHERE id = :id");

		$stmt -> bindParam(":id", $data, PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}
	/* --LOG ON TO codeastro.com FOR MORE PROJECTS-- */
	/*=============================================
	DATES RANGE
	=============================================*/	

	static public function mdlSalesDatesRange($table, $initialDate, $finalDate){

		if($initialDate == null){

			$stmt = Connection::connect()->prepare("SELECT * FROM $table ORDER BY id ASC");

			$stmt -> execute();

			return $stmt -> fetchAll();	


		}else if($initialDate == $finalDate){

			$stmt = Connection::connect()->prepare("SELECT * FROM $table WHERE saledate like '%$finalDate%'");

			$stmt -> bindParam(":saledate", $finalDate, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

		}else{

			$actualDate = new DateTime();
			$actualDate ->add(new DateInterval("P1D"));
			$actualDatePlusOne = $actualDate->format("Y-m-d");

			$finalDate2 = new DateTime($finalDate);
			$finalDate2 ->add(new DateInterval("P1D"));
			$finalDatePlusOne = $finalDate2->format("Y-m-d");

			if($finalDatePlusOne == $actualDatePlusOne){

				$stmt = Connection::connect()->prepare("SELECT * FROM $table WHERE saledate BETWEEN '$initialDate' AND '$finalDatePlusOne'");

			}else{


				$stmt = Connection::connect()->prepare("SELECT * FROM $table WHERE saledate BETWEEN '$initialDate' AND '$finalDate'");

			}
		
			$stmt -> execute();

			return $stmt -> fetchAll();

		}

	}

	/* --LOG ON TO codeastro.com FOR MORE PROJECTS-- */
	/*=============================================
	Adding TOTAL sales
	=============================================*/

	static public function mdlAddingTotalSales($table){	

		$stmt = Connection::connect()->prepare("SELECT SUM(totalPrice) as total FROM $table");

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;

	}
}