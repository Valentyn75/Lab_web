<?php
session_start();

// 1. Ініціалізація або скидання гри
if (!isset($_SESSION['board']) || isset($_GET['reset'])) {
    $_SESSION['board'] = array_fill(0, 9, ''); // Поле з 9 порожніх клітинок
    $_SESSION['turn'] = 'X';                   // Першим ходить X
    $_SESSION['winner'] = null;                // Переможця ще немає
    $_SESSION['draw'] = false;                 // Нічиєї немає
}

// 2. Обробка ходу гравця
if (isset($_POST['cell']) && $_SESSION['winner'] == null) {
    $cellIndex = $_POST['cell'];

    // Перевіряємо, чи клітинка порожня
    if ($_SESSION['board'][$cellIndex] === '') {
        $_SESSION['board'][$cellIndex] = $_SESSION['turn'];
        
        // Перевірка переможця
        checkWinner();

        // Якщо переможця немає, перевіряємо на нічию
        if ($_SESSION['winner'] == null && !in_array('', $_SESSION['board'])) {
            $_SESSION['draw'] = true;
        }

        // Зміна черги (якщо гра триває)
        if ($_SESSION['winner'] == null && !$_SESSION['draw']) {
            $_SESSION['turn'] = ($_SESSION['turn'] === 'X') ? 'O' : 'X';
        }
    }
}

// 3. Функція для перевірки переможних комбінацій
function checkWinner() {
    $winPatterns = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // Горизонталі
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // Вертикалі
        [0, 4, 8], [2, 4, 6]             // Діагоналі
    ];

    foreach ($winPatterns as $pattern) {
        if ($_SESSION['board'][$pattern[0]] !== '' &&
            $_SESSION['board'][$pattern[0]] === $_SESSION['board'][$pattern[1]] &&
            $_SESSION['board'][$pattern[0]] === $_SESSION['board'][$pattern[2]]) {
            
            $_SESSION['winner'] = $_SESSION['board'][$pattern[0]];
            return;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Хрестики-Нолики на PHP</title>
    <style>
        body { font-family: 'Arial', sans-serif; text-align: center; background: #f0f2f5; }
        .game-container { margin-top: 50px; }
        .board { 
            display: grid; 
            grid-template-columns: repeat(3, 100px); 
            gap: 5px; 
            justify-content: center; 
            margin: 20px auto; 
        }
        .cell-btn {
            width: 100px;
            height: 100px;
            font-size: 40px;
            cursor: pointer;
            background: #fff;
            border: 2px solid #333;
            transition: 0.3s;
        }
        .cell-btn:hover { background: #e9ecef; }
        .status { font-size: 24px; margin-bottom: 10px; font-weight: bold; }
        .winner { color: green; font-size: 28px; }
        .draw { color: orange; }
        .reset-link { 
            display: inline-block; 
            margin-top: 20px; 
            padding: 10px 20px; 
            background: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
        }
    </style>
</head>
<body>

<div class="game-container">
    <h1>Хрестики-Нолики на PHP</h1>

    <div class="status">
        <?php if ($_SESSION['winner']): ?>
            <span class="winner">Переміг: <?php echo $_SESSION['winner']; ?>! 🎉</span>
        <?php elseif ($_SESSION['draw']): ?>
            <span class="draw">Нічия! 🤝</span>
        <?php else: ?>
            Зараз ходить: <?php echo $_SESSION['turn']; ?>
        <?php endif; ?>
    </div>

    <form method="post" action="index.php">
        <div class="board">
            <?php foreach ($_SESSION['board'] as $index => $value): ?>
                <button type="submit" name="cell" value="<?php echo $index; ?>" 
                        class="cell-btn" 
                        <?php echo ($value !== '' || $_SESSION['winner'] || $_SESSION['draw']) ? 'disabled' : ''; ?>>
                    <?php echo $value; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </form>

    <a href="index.php?reset=1" class="reset-link">Почати заново</a>
</div>

</body>
</html>