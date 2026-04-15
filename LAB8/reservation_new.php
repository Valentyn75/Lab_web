<?php
require_once '_db.php';
// Отримуємо параметри з URL (DayPilot передає їх автоматично)
$start = $_GET['start'];
$end = $_GET['end'];
$resource = $_GET['resource'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Нове бронювання</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .field { margin-bottom: 10px; }
        label { display: block; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 20px; background: #2ecc71; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h3>Нове бронювання</h3>
    <form id="f">
        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($resource); ?>">
        
        <div class="field">
            <label>Ім'я клієнта</label>
            <input type="text" name="name" required>
        </div>
        
        <div class="field">
            <label>Початок</label>
            <input type="text" name="start" value="<?php echo htmlspecialchars($start); ?>">
        </div>
        
        <div class="field">
            <label>Кінець</label>
            <input type="text" name="end" value="<?php echo htmlspecialchars($end); ?>">
        </div>

        <div class="field">
            <label>Статус</label>
            <select name="status">
                <option value="New">Нове</option>
                <option value="Confirmed">Підтверджено</option>
            </select>
        </div>

        <button type="submit">Зберегти</button>
        <button type="button" onclick="DayPilot.Modal.close();">Скасувати</button>
    </form>

    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script>
        $("#f").submit(function(ev) {
            ev.preventDefault();
            $.post("backend_create.php", $(this).serialize(), function(result) {
                DayPilot.Modal.close(result);
            });
        });
    </script>
</body>
</html>