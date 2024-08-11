<?php 
if(session_id() == '') {
    session_start();
}

require_once('controllers/all_Controllers.php');
require_once('models/all_Models.php');
