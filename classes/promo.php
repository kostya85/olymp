<?php
class Promo {
	private $conn;
	private $table_name = "promotions";
	
	// конструктор для соединения с базой данных
	public function __construct($db){
		$this->conn = $db;
	}
	
	//создание промоакции
	function create($name, $description = ''){
		$query = "INSERT INTO
                " . $this->table_name . "
            SET
                name=:name, description=:description";
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// очистка
		$name=htmlspecialchars(strip_tags($name));
		$description=htmlspecialchars(strip_tags($description));
		
		// привязка значений
		$stmt->bindParam(":name", $name);;
		$stmt->bindParam(":description", $description);
		
		// выполняем запрос
		if ($stmt->execute()) {
			return $this->getLastId();
		}
		
		return false;
	}
	
	//получение всех промоакций
	function getAllPromo(){
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
				"description" => html_entity_decode($description),
			);
			
			array_push($arRecords, $product_item);
		}
		
		return $arRecords;
	}
	
	//получение подробной информации о промоакции
	function getPromo($id){
		$query = "SELECT
                *
            FROM
                " . $this->table_name . ' WHERE id = ' . $id . ' LIMIT 1;';
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// выполняем запрос
		$stmt->execute();
		
		/* получение значения */
		$record = $stmt->fetch();
		
		return [
			'id' => $record['id'],
			'name' => $record['name'],
			'description' => $record['description'],
			'prizes' => empty($record['prizes']) ? [] : unserialize($record['prizes']),
			'participants' => empty($record['participants']) ? [] : unserialize($record['participants']),
		];
	}
	
	function updatePromo($id, $name, $description = ''){
		$query = "UPDATE " . $this->table_name . "
            SET
                name=:name, description=:description,
            WHERE
                id=:id;";
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// очистка
		$name=htmlspecialchars(strip_tags($name));
		$description=htmlspecialchars(strip_tags($description));
		$id=htmlspecialchars(strip_tags($id));

		// привязываем значения
		$stmt->bindParam(':name', $name);
		$stmt->bindParam(':description', $description);
		$stmt->bindParam(':id', $id);
		
		// выполняем запрос
		if ($stmt->execute()) {
			return true;
		}
		
		return false;
	}
	
	function deletePromo($id){
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
	
	function addParticipant($promoId, $participantId){
		$promo = $this->getPromo($promoId);
		
		$arParticipants = empty($promo['participants']) ? [] : unserialize($promo['participants']);
		
		$arParticipants[] = $participantId;
		
		$query = "UPDATE " . $this->table_name . "
            SET
                participants=:participants,
            WHERE
                id=:id;";
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// привязываем значения
		$stmt->bindParam(':participants', serialize($arParticipants));
		$stmt->bindParam(':id', $promoId);
		
		// выполняем запрос
		if ($stmt->execute()) {
			return true;
		}
		
		return false;
	}
	
	function deleteParticipant($promoId, $participantId){
		$promo = $this->getPromo($promoId);
		
		if(empty($promo['participants'])) return true;
		
		$arParticipants = unserialize($promo['participants']);
		
		foreach($arParticipants as $id => $participant){
			if($participant == $participantId) unset($arParticipants[$id]);
		}
		
		$query = "UPDATE " . $this->table_name . "
            SET
                participants=:participants,
            WHERE
                id=:id;";
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// привязываем значения
		$stmt->bindParam(':participants', serialize($arParticipants));
		$stmt->bindParam(':id', $promoId);
		
		// выполняем запрос
		if ($stmt->execute()) {
			return true;
		}
		
		return false;
	}
	
	function addPrize($promoId, $prizeId){
		
		$promo = $this->getPromo($promoId);
		
		$arPrizes = empty($promo['prizes']) ? [] : unserialize($promo['prizes']);
		
		$arPrizes[] = $prizeId;
		
		$query = "UPDATE " . $this->table_name . "
            SET
                prizes=:prizes,
            WHERE
                id=:id;";
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// привязываем значения
		$stmt->bindParam(':prizes', serialize($arPrizes));
		$stmt->bindParam(':id', $promoId);
		
		// выполняем запрос
		if ($stmt->execute()) {
			return true;
		}
		
		return false;
	}
	
	function deletePrize($promoId, $prizeId){
		$promo = $this->getPromo($promoId);
		
		if(empty($promo['prizes'])) return true;
		
		$arPrizes = unserialize($promo['prizes']);
		
		foreach($arPrizes as $id => $prize){
			if($prize == $prizeId) unset($arPrizes[$id]);
		}
		
		$query = "UPDATE " . $this->table_name . "
            SET
                participants=:participants,
            WHERE
                id=:id;";
		
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
		
		// привязываем значения
		$stmt->bindParam(':participants', serialize($arPrizes));
		$stmt->bindParam(':id', $promoId);
		
		// выполняем запрос
		if ($stmt->execute()) {
			return true;
		}
		
		return false;
	}
	
	function raffle($promoId){
		$promo = $this->getPromo($promoId);
		
		if(empty($promo['prizes']) or empty($promo['participants'])) return false;
		
		$arPrizes = unserialize($promo['prizes']);
		$arParticipants = unserialize($promo['participants']);
		
		$arPrizesInfo = (new Prize($this->conn))->getAll();
		$arParticipantsInfo = (new Participant($this->conn))->getAll();
		
		if(count($arPrizes) !== count($arParticipants)) return false;
		
		shuffle($arParticipants);
		shuffle($arPrizes);
		
		$arResult = [];
		
		foreach($arPrizes as $id => $arPrize){
			foreach($arParticipantsInfo as $item){
				if($item['id'] == $arParticipants[$id]){
					$name = $item['name'];
				}
			}
			
			foreach($arPrizesInfo as $item){
				if($item['id'] == $arPrize){
					$description = $item['description'];
				}
			}
			
			$arResult[] = [
				'winner' => [
					'id' => $arParticipants[$id],
					'name' => $name
				],
				'prize' => [
					'id' => $arPrize,
					'description' => $description
				]
			];
		}
		
		return $arResult;
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