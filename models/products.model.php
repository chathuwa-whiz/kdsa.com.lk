<?php

require_once 'connection.php';

class ProductsModel{
	
	/*=============================================
	SHOWING PRODUCTS
	=============================================*/

	static public function mdlShowProducts($table, $item, $value, $order){

		if($item != null){

			$stmt = Connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Connection::connect()->prepare("SELECT * FROM $table ORDER BY $order DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}
	
	/*=============================================
	ADDING PRODUCT
	=============================================*/
	static public function mdlAddProduct($table, $data){

		$stmt = Connection::connect()->prepare("INSERT INTO $table(idCategory, code, description, image, stock, buyingPrice, sellingPrice, discountPrice, sales) VALUES (:idCategory, :code, :description, :image, :stock, :buyingPrice, :sellingPrice, :discountPrice, 0)");

		$stmt->bindParam(":idCategory", $data["idCategory"], PDO::PARAM_INT);
		$stmt->bindParam(":code", $data["code"], PDO::PARAM_STR);
		$stmt->bindParam(":description", $data["description"], PDO::PARAM_STR);
		$stmt->bindParam(":image", $data["image"], PDO::PARAM_STR);
		$stmt->bindParam(":stock", $data["stock"], PDO::PARAM_STR);
		$stmt->bindParam(":buyingPrice", $data["buyingPrice"], PDO::PARAM_STR);
		$stmt->bindParam(":sellingPrice", $data["sellingPrice"], PDO::PARAM_STR);
		$stmt->bindParam(":discountPrice", $data["discountPrice"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}
	
	/*=============================================
	EDITING PRODUCT
	=============================================*/
	static public function mdlEditProduct($table, $data){

		$stmt = Connection::connect()->prepare("UPDATE $table SET idCategory = :idCategory, description = :description, image = :image, stock = :stock, buyingPrice = :buyingPrice, sellingPrice = :sellingPrice, discountPrice = :discountPrice WHERE code = :code");
		
		$stmt->bindParam(":idCategory", $data["idCategory"], PDO::PARAM_INT);
		$stmt->bindParam(":code", $data["code"], PDO::PARAM_STR);
		$stmt->bindParam(":description", $data["description"], PDO::PARAM_STR);
		$stmt->bindParam(":image", $data["image"], PDO::PARAM_STR);
		$stmt->bindParam(":stock", $data["stock"], PDO::PARAM_STR);
		$stmt->bindParam(":buyingPrice", $data["buyingPrice"], PDO::PARAM_STR);
		$stmt->bindParam(":sellingPrice", $data["sellingPrice"], PDO::PARAM_STR);
		$stmt->bindParam(":discountPrice", $data["discountPrice"], PDO::PARAM_STR);
		
		
		// Step 1: Retrieve current stock value
		$stmtSelect = Connection::connect()->prepare("SELECT stock FROM $table WHERE code = :code");
		$stmtSelect->bindParam(":code", $data["code"], PDO::PARAM_STR);
		$stmtSelect->execute();
		$currentData = $stmtSelect->fetch(PDO::FETCH_ASSOC);
		$currentStock = $currentData['stock'];
		echo '<script>console.log("current stock '.$currentStock.'");</script>';
		
		if($stmt->execute()){
			
			echo '<script>console.log("new stock '.$data["stock"].'");</script>';
			
			// Step 2: Check if stock value has changed
			if ($currentStock != $data["stock"]) {
				// Step 3: Insert product details into stock table
				$stmtInsert = Connection::connect()->prepare("INSERT INTO stock (productName, stockCount, date) VALUES (:productName, :stockCount, NOW())");
				$stmtInsert->bindParam(":productName", $data["code"], PDO::PARAM_STR);
				$stmtInsert->bindParam(":stockCount", $data["stock"], PDO::PARAM_INT);
				if($stmtInsert->execute())
				{
					return "ok";

				}
			}
			
			return "ok";
			
		}else{
			
			return "error";
		
		}


		$stmt->close();
		$stmtInsert->close();
		$stmtInsert = null;
		$stmt = null;

	}
	
	/*=============================================
	DELETING PRODUCT
	=============================================*/

	static public function mdlDeleteProduct($table, $data){

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
	
	/*=============================================
	UPDATE PRODUCT
	=============================================*/

	static public function mdlUpdateProduct($table, $item1, $value1, $value){

		$stmt = Connection::connect()->prepare("UPDATE $table SET $item1 = :$item1 WHERE id = :id");

		$stmt -> bindParam(":".$item1, $value1, PDO::PARAM_STR);
		$stmt -> bindParam(":id", $value, PDO::PARAM_STR);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}
	
	/*=============================================
	SHOW ADDING OF THE SALES
	=============================================*/	

	static public function mdlShowAddingOfTheSales($table){

		$stmt = Connection::connect()->prepare("SELECT SUM(sales) as total FROM $table");

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;
	}


	
}