<?php
class Participant {
	private $conn;
	private $table_name = "competitors";
	
	// конструктор для соединения с базой данных
	public function __construct($db){
		$this->conn = $db;
	}
	
	//создание промоакции
	function create($name){
		$query = "INSERT INTO
                " . $this->table_name . "
            SET
                name=:name";
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// очистка
		$name=htmlspecialchars(strip_tags($name));
		
		// привязка значений
		$stmt->bindParam(":name", $name);;
		
		// выполняем запрос
		if ($stmt->execute()) {
			return $this->getLastId();
		}
		
		return false;
	}
	
	function delete($id){
		$query = "DELETE FROM
                " . $this->table_name . "
            WHERE
                id=:id;";
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// очистка
		$id=htmlspecialchars(strip_tags($id));
		
		// привязываем значения
		$stmt->bindParam(':id', $id);
		
		// выполняем запрос
		if ($stmt->execute()) {
			return true;
		}
		
		return false;
	}
	
	function getAll(){
		$query = "SELECT
                id, name, description
            FROM
                " . $this->table_name . ' ;';
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// выполняем запрос
		$stmt->execute();
		
		/* получение значения */
		$arRecords = [];
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			
			// извлекаем строку
			extract($row);
			
			$product_item=array(
				"id" => $id,
				"name" => $name,
			);
			
			array_push($arRecords, $product_item);
		}
		
		return $arRecords;
	}
	
	//получение id последнего добавленного элемента
	function getLastId(){
		$query = "SELECT
                id
            FROM
                " . $this->table_name . ' ORDER BY id DESC LIMIT 1;';
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// выполняем запрос
		$stmt->execute();
		
		/* получение значения */
		$record = $stmt->fetch();
		
		return $record['id'];
	}
}
?>