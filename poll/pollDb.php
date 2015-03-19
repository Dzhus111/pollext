<?php
    namespace pollext\poll;
    use yii;
    
    class PollDb{
        
        
         public function isPollExist($pollName){
            $db = Yii::$app->db;
            $command = $db->createCommand('SELECT * FROM polls WHERE poll_name=:pollName')->
            bindParam(':pollName',$pollName);
            
            $pollData = $command->queryOne();
            
            if($pollData==null){
                return false;
            }else {
                return true;
            }
         }
         
         public function setVoicesData($pollName, $answerOptions){
            $db = Yii::$app->db;
            $answersList = array();
            
            for($i=0; $i<count($answerOptions); $i++){
               $command = $db->createCommand()->insert('voices_of_poll', [
                'poll_name' => $pollName,
                'answers' => $answerOptions[$i],
                'value' => 0
                ])->execute(); 
            }   
            
            
         }
         
         public function getVoicesData($pollName){
            $db = Yii::$app->db;
            $command = $db->createCommand('SELECT * FROM voices_of_poll WHERE poll_name=:pollName')->
            bindParam(':pollName',$pollName);
            $voicesData = $command->queryAll();
            return $voicesData;
            
         }
         
         public function updateAnswers($pollName, $voise, $answerOptions){
            if(isset($_POST['VoicesOfPoll']['voice'])){
                $db = Yii::$app->db;
                ;
                /*if(array_search(Yii::$app->user->getId(),$ids)==false){
                    
                }else{
                    return false;
                }
                */
                
                $command = $db->createCommand("UPDATE voices_of_poll SET value=value +1  
                         WHERE poll_name='$pollName' AND answers='$answerOptions[$voise]'")->execute();
                
            }
         }
         
        public function updateUsers($pollName){
            $db = Yii::$app->db;
            $command = $db->createCommand('SELECT * FROM polls WHERE poll_name=:pollName')->
            bindParam(':pollName',$pollName);
            $userId;
            if(Yii::$app->user->getId()==null){
                $userId = 0;
            }else{
                $userId = Yii::$app->user->getId(); 
            }
            $pollData = $command->queryOne();
            $command = $db->createCommand()->insert('users_id', [
                'poll_id' => $pollData['id'],
                'user_id' => $userId
                ])->execute(); 
            
        }
        public  function isVote($pollName){
            $db = Yii::$app->db;
            $command = $db->createCommand('SELECT * FROM polls WHERE poll_name=:pollName')->
            bindParam(':pollName',$pollName);
            $pollData = $command->queryOne();
            $userId;
            if(Yii::$app->user->getId()== null){
                $userId = 0;
            }else{
                $userId = Yii::$app->user->getId(); 
            }
            $db = Yii::$app->db;
            $command = $db->createCommand("SELECT * FROM users_id WHERE user_id='$userId' AND poll_id=:pollId")->
            bindParam(':pollId',$pollData['id']);
            $result = $command->queryOne();
            
            if($result == null){
                return false;
            }else{
                return true;
            }
        }
        
        public function createTables(){
            $db = Yii::$app->db;
            $command_1 = $db->createCommand("
                        CREATE TABLE IF NOT EXISTS `users_id` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `poll_id` int(11) NOT NULL,
                        `user_id` int(11) NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `poll_id` (`poll_id`)
                        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8"
                        )->execute();
                        
            $command_2 = $db->createCommand("
                        CREATE TABLE IF NOT EXISTS `polls` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `poll_name` varchar(500) NOT NULL,
                        `answer_options` text NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `poll_name` (`poll_name`(255))
                        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8"
                        )->execute();
           
           $command_3 = $db->createCommand("
                        CREATE TABLE IF NOT EXISTS `voices_of_poll` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `poll_name` varchar(500) NOT NULL,
                        `answers` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
                        `value` int(11) NOT NULL,
                        PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8"
                        )->execute(); 
        }
        
        public function isTableExists(){
            $db = Yii::$app->db;
            $command = $db->createCommand("SHOW TABLES LIKE 'polls'");
            $res = $command->queryAll();
            return $res;
        }
        
    }