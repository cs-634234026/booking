<?php
// Include database configuration file  
require_once 'dbConfig.php';

// Retrieve JSON from POST body 
$jsonStr = file_get_contents('php://input');
$jsonObj = json_decode($jsonStr);


if ($jsonObj->request_type == 'addEvent') {

     $start = $jsonObj->start;
     $end = $jsonObj->end;

     $event_data = $jsonObj->event_data;
     $name = !empty($event_data[0]) ? $event_data[0] : '';
     $phone = !empty($event_data[1]) ? $event_data[1] : '';
     $deposit = !empty($event_data[3]) ? $event_data[3] : '';
     $note = !empty($event_data[4]) ? $event_data[4] : '';
     $color = $event_data[5];

    $start = $jsonObj->start;
    $end = $jsonObj->end;

    $event_data = $jsonObj->event_data;
    $name = !empty($event_data[0]) ? $event_data[0] : '';
    $phone = !empty($event_data[1]) ? $event_data[1] : '';
    $deposit = !empty($event_data[3]) ? $event_data[3] : '';
    $note = !empty($event_data[4]) ? $event_data[4] : '';
    $color = $event_data[5];

    $where_sql = " WHERE start BETWEEN '" . $start . "' AND '" .  $end . "' OR end BETWEEN '" . $start . "' AND '" .  $end . "'";

    // $sql = "SELECT * FROM events WHERE start BETWEEN $start AND $end OR end BETWEEN $start AND $end";
    $sql = "SELECT * FROM events $where_sql";
    $result = $db->query($sql);

    if ($result->num_rows > 8) {
        echo json_encode(['error' => 'วันที่นี้ถูกจองแล้ว']);
    } else {
        // Insert event data into the database 
        $sqlQ = "INSERT INTO events (name,phone,deposit,start,end,note,color) VALUES (?,?,?,?,?,?,?)";
        $stmt = $db->prepare($sqlQ);
        $stmt->bind_param("sssssss", $name, $phone, $deposit, $start, $end, $note, $color);
        $insert = $stmt->execute();

        if ($insert) {
            $output = [
                'status' => 1
            ];
            echo json_encode($output);
        } else {
            echo json_encode(['error' => 'Event Add request failed!']);
        }
    }
} elseif ($jsonObj->request_type == 'deleteEvent') {
    $id = $jsonObj->event_id;
// Check if the user is the owner of the event
    $sql = "SELECT * FROM events WHERE id = $id";
    $result = $db->query($sql);


    //// Delete event from the database
    $sql = "DELETE FROM events WHERE id=$id";
    $delete = $db->query($sql);
    if ($delete) {
        $output = [
            'status' => 1
        ];
        echo json_encode($output);
    } else {
        echo json_encode(['error' => 'Event Delete request failed!']);
    }
}

