<?php
// questions.php
return json_encode([
    [
        'id' => 1,
        'text' => 'Five children A, B, C, D, and E are standing in ascending order of their heights. C is shorter than B but taller than D. A is taller than E but shorter than C. D is the second shortest. Who is the shortest?',
        'options' => [
            'E',
            'B',
            'C',
            'None'
        ],
        'correct_answer' => 'E'
    ],
    // Add more questions...
]);
?>