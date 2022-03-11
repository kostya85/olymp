<?php

// Роутер
function route($method, $urlData, $formData) {
	try{
		include_once 'config/database.php';
		include_once 'classes/promo.php';
		include_once 'classes/participant.php';
		include_once 'classes/prize.php';
		
		http_response_code(200);
		
		// получаем соединение с базой данных
		$database = new Database();
		$db = $database->getConnection();
		
		$promo = new Promo($db);
		
		$participant = new Participant($db);
		$prize = new Prize($db);
		
		// Получение информации о всех промоакциях
		// GET /promo
		if ($method === 'GET' && empty($urlData)) {
			$arRecords = $promo->getAllPromo();
			
			// Выводим ответ клиенту
			echo json_encode($arRecords);
			
			return;
		}
		
		// Получение информации о промоакции
		// GET /promo/{id}
		if ($method === 'GET' && count($urlData) === 1) {
			$promoId = $urlData[0];
			
			$promoInfo = $promo->getPromo($promoId);
			
			$arPrizesInfo = (new Prize($this->conn))->getAll();
			$arParticipantsInfo = (new Participant($this->conn))->getAll();
			
			$arPrizes = [];
			$arParticipantsInfo = [];
			
			foreach($promoInfo['prizes'] as $prize){
				foreach($arPrizesInfo as $item){
					if($item['id'] == $prize){
						$arPrizes[] = $item;
						break;
					}
				}
			}
			
			$promoInfo['prizes'] = $arPrizes;
			
			foreach($promoInfo['participants'] as $participant){
				foreach($arParticipantsInfo as $item){
					if($item['id'] == $participant){
						$arParticipants[] = $item;
						break;
					}
				}
			}
			
			$promoInfo['participants'] = $arParticipants;
			
			// Выводим ответ клиенту
			echo json_encode($promoInfo);
			
			return;
		}
		
		
		// Добавление новой промоакции
		// POST /promo
		if ($method === 'POST' && empty($urlData)) {
			if(empty($formData['name'])){
				throw new Exception('Не указано название промоакции');
			}
			
			// Добавляем промоакцию в базу
			$lastId = $promo->create($formData['name'], $formData['description']);
		
			// Выводим ответ клиенту
			echo json_encode(array(
				'id' => $lastId,
			));
			
			return;
		}
		
		// Добавление участника
		// POST /promo/{id}/participant
		if ($method === 'POST' && count($urlData) === 2 && in_array('participant', $urlData)) {
			if(empty($formData['name'])){
				throw new Exception('Не указано имя участника');
			}
			$participantId = $participant->create($formData['name']);
			
			$res = $promo->addParticipant($urlData[0], $participantId);
			
			if(!$res){
				throw new Exception('Не удалось добавить участника');
			}
			
			// Выводим ответ клиенту
			echo json_encode(array(
				'status' => true
			));
			
			return;
		}
		
		// Добавление приза
		// POST /promo/{id}/prize
		if ($method === 'POST' && count($urlData) === 2 && in_array('prize', $urlData)) {
			if(empty($formData['description'])){
				throw new Exception('Не указано название приза');
			}
			$prizeId = $prize->create($formData['description']);
			
			$res = $promo->addParticipant($urlData[0], $prizeId);
			
			if(!$res){
				throw new Exception('Не удалось добавить приз');
			}
			
			// Выводим ответ клиенту
			echo json_encode(array(
				'status' => true
			));
			
			return;
		}
		
		// Проведение розыгрыша
		// POST promo/{id}/raffle
		if ($method === 'POST' && count($urlData) === 2 && in_array('raffle', $urlData)) {
			$promoId = $urlData[0];
			
			
			
			if(!$res){
				throw new Exception('Не удалось добавить приз');
			}
			
			// Выводим ответ клиенту
			echo json_encode(array(
				'status' => true
			));
			
			return;
		}
		
		// Обновление данный промоакции
		// PUT /promo/{id}
		if ($method === 'PUT' && count($urlData) === 1) {
			$promoId = $urlData[0];
			
			if(empty($formData['name'])){
				throw new Exception('Не указано название промоакции');
			}
			
			$res = $promo->updatePromo($promoId, $formData['name'], $formData['description']);
			
			if(!$res){
				throw new Exception('Не удалось обновить запись ' . $promoId);
			}
			
			// Выводим ответ клиенту
			echo json_encode(array(
				'status' => true
			));
			
			return;
		}
		
		// Удаление промоакции
		// DELETE /promo/{id}
		if ($method === 'DELETE' && count($urlData) === 1) {
			$promoId = $urlData[0];
			
			$res = $promo->deletePromo($promoId);
			
			if(!$res){
				throw new Exception('Не удалось удалить запись ' . $promoId);
			}
			
			// Выводим ответ клиенту
			echo json_encode(array(
				'status' => true
			));
			
			return;
		}
		
		// Удаление участника промоакции
		// DELETE /promo/{promoId}/participant/{participantId}
		if ($method === 'DELETE' && count($urlData) === 3 && in_array('participant', $urlData)) {
			$promoId = $urlData[0];
			$participantId = $urlData[2];
			
			$res = $promo->deleteParticipant($promoId, $participantId);
			
			if(!$res){
				throw new Exception('Не удалось удалить участника ' . $promoId);
			}
			
			// Выводим ответ клиенту
			echo json_encode(array(
				'status' => true
			));
			
			return;
		}
		
		// Удаление приза
		// DELETE /promo/{promoId}/prize/{prizeId}
		if ($method === 'DELETE' && count($urlData) === 3 && in_array('prize', $urlData)) {
			$promoId = $urlData[0];
			$prizeId = $urlData[2];
			
			$res = $promo->deleteParticipant($promoId, $prizeId);
			
			if(!$res){
				throw new Exception('Не удалось удалить приз ' . $promoId);
			}
			
			// Выводим ответ клиенту
			echo json_encode(array(
				'status' => true
			));
			
			return;
		}
	}catch(Exception $exception){
		// Возвращаем ошибку
		header('HTTP/1.0 400 Bad Request');
		echo json_encode(array(
			'status' => false,
			'error' => $exception->getMessage()
		));
	}
}
