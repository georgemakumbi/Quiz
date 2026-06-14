<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$name = htmlspecialchars($_SESSION['user']['name']);
$userId = $_SESSION['user']['id'];
$score = null;
$maxScore = 10;
$feedback = '';

$storedScore = null;
$stmt = $pdo->prepare('SELECT score FROM users WHERE id = ?');
$stmt->execute([$userId]);
$row = $stmt->fetch();
if ($row) {
    $storedScore = $row['score'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correctAnswers = [
        'q1' => 'a',
        'q2' => 'b',
        'q3' => 'b',
        'q4' => 'a',
        'q5' => 'b',
        'q6' => 'b',
        'q7' => 'a',
        'q8' => 'b',
        'q9' => 'b',
        'q10' => 'c',
    ];

    $score = 0;
    foreach ($correctAnswers as $question => $answer) {
        if (isset($_POST[$question]) && $_POST[$question] === $answer) {
            $score++;
        }
    }

    $feedback = sprintf('You scored %d out of %d.', $score, $maxScore);
    if ($score === $maxScore) {
        $feedback .= ' Perfect score!';
    } elseif ($score >= 7) {
        $feedback .= ' Great job!';
    } else {
        $feedback .= ' Keep practicing to improve your score.';
    }

    $update = $pdo->prepare('UPDATE users SET score = ? WHERE id = ?');
    $update->execute([$score, $userId]);
    $storedScore = $score;
    $feedback .= ' Your score has been saved.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Welcome<?php echo $name ? ' ' . $name : ''; ?>!</h1>
    <p>Your account has been created successfully. Enjoy your stay on the home page.</p>

    <?php if ($score !== null): ?>
        <section class="result">
            <h2>Quiz Result</h2>
            <p><?php echo htmlspecialchars($feedback); ?></p>
        </section>
    <?php endif; ?>

    <section>
        <h2>Quick Quiz</h2>
        <p>Answer these 10 questions to test your knowledge.</p>
        <form method="post">
            <ol>
                <li>
                    <p>What does HTML stand for?</p>
                    <label><input type="radio" name="q1" value="a"> HyperText Markup Language</label><br>
                    <label><input type="radio" name="q1" value="b"> HyperText Markdown Language</label><br>
                    <label><input type="radio" name="q1" value="c"> HighText Machine Language</label>
                </li>
                <li>
                    <p>Which tag is used for an unordered list?</p>
                    <label><input type="radio" name="q2" value="b"> &lt;ul&gt;</label><br>
                    <label><input type="radio" name="q2" value="a"> &lt;ol&gt;</label><br>
                    <label><input type="radio" name="q2" value="c"> &lt;li&gt;</label>
                </li>
                <li>
                    <p>Which CSS property changes text color?</p>
                    <label><input type="radio" name="q3" value="b"> color</label><br>
                    <label><input type="radio" name="q3" value="a"> font-size</label><br>
                    <label><input type="radio" name="q3" value="c"> background-color</label>
                </li>
                <li>
                    <p>What is the correct way to declare a PHP variable?</p>
                    <label><input type="radio" name="q4" value="a"> $variable</label><br>
                    <label><input type="radio" name="q4" value="b"> var variable</label><br>
                    <label><input type="radio" name="q4" value="c"> #variable</label>
                </li>
                <li>
                    <p>Which HTML element is used to define a paragraph?</p>
                    <label><input type="radio" name="q5" value="b"> &lt;p&gt;</label><br>
                    <label><input type="radio" name="q5" value="a"> &lt;para&gt;</label><br>
                    <label><input type="radio" name="q5" value="c"> &lt;text&gt;</label>
                </li>
                <li>
                    <p>Which attribute specifies an image source in HTML?</p>
                    <label><input type="radio" name="q6" value="b"> src</label><br>
                    <label><input type="radio" name="q6" value="a"> href</label><br>
                    <label><input type="radio" name="q6" value="c"> alt</label>
                </li>
                <li>
                    <p>What does CSS stand for?</p>
                    <label><input type="radio" name="q7" value="a"> Cascading Style Sheets</label><br>
                    <label><input type="radio" name="q7" value="b"> Computer Style Sheets</label><br>
                    <label><input type="radio" name="q7" value="c"> Creative Style System</label>
                </li>
                <li>
                    <p>Which HTML element is used to create a link?</p>
                    <label><input type="radio" name="q8" value="b"> &lt;a&gt;</label><br>
                    <label><input type="radio" name="q8" value="a"> &lt;link&gt;</label><br>
                    <label><input type="radio" name="q8" value="c"> &lt;href&gt;</label>
                </li>
                <li>
                    <p>Which symbol is used to end a PHP statement?</p>
                    <label><input type="radio" name="q9" value="b"> ;</label><br>
                    <label><input type="radio" name="q9" value="a"> .</label><br>
                    <label><input type="radio" name="q9" value="c"> :</label>
                </li>
                <li>
                    <p>Which HTML element is used to embed JavaScript?</p>
                    <label><input type="radio" name="q10" value="c"> &lt;script&gt;</label><br>
                    <label><input type="radio" name="q10" value="a"> &lt;js&gt;</label><br>
                    <label><input type="radio" name="q10" value="b"> &lt;javascript&gt;</label>
                </li>
            </ol>
            <button type="submit">Submit Answers</button>
        </form>
    </section>
</body>
</html>
