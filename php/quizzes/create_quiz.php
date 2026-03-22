<?php

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$title = $data["title"];
$description = $data["description"];
$time = $data["time_limit"];
$questions = $data["questions"];

if (!$title || !$questions) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing data"
    ]);
    exit;
}

$sql = "INSERT INTO quizzes (title, description, time_limit)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ssi", $title, $description, $time);

$stmt->execute();

$quiz_id = $stmt->insert_id;

foreach ($questions as $q) {

    $sql = "INSERT INTO questions
        (quiz_id, question,
         option_a, option_b,
         option_c, option_d,
         correct_answer)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "issssss",
        $quiz_id,
        $q["question"],
        $q["optionA"],
        $q["optionB"],
        $q["optionC"],
        $q["optionD"],
        $q["correctAnswer"]
    );

    $stmt->execute();
}

echo json_encode([
    "status" => "success"
]);

?>