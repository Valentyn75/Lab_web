<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Management System (SPA) - Panchenko Valentyn</title>
   
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="https://javascript.daypilot.org/demo/js/daypilot-all.min.js?v=2024"></script>
   
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        .status-bar {
            display: inline-block;
            width: 6px;
            height: 40px;
            margin-right: 10px;
            border-radius: 3px;
            vertical-align: middle;
        }
        .bar-Dirty   { background-color: #e74c3c; }
        .bar-Cleanup { background-color: #f1c40f; }
        .bar-Ready   { background-color: #2ecc71; }

        .scheduler_default_event {
            cursor: pointer;
            border-radius: 2px;
        }
        .scheduler_default_event_inner {
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
<header>
    <div style="background:#2c3e50; color:white; padding:20px; border-bottom: 4px solid #3498db;">
        <h1>Програма резервування кімнат у готелі: постановка задачі</h1>
        <p>Створення веб-програми з використанням HTML5, CSS3, JavaScript, PHP, MySQL.</p>
    </div>
</header>

<main style="padding: 20px;">
   
    <div class="toolbar" style="margin-bottom: 20px;">
        <label>Month: </label>
        <select id="selectMonth" style="padding: 6px; margin-right: 15px;">
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>

        <script>
            document.getElementById('selectMonth').value = new Date().getMonth() + 1;
        </script>

        <label>Year: </label>
        <select id="selectYear" style="padding: 6px; margin-right: 15px;">
            <?php
            $currentYear = date("Y");
            for($i = 2015; $i <= 2030; $i++) {
                $selected = ($i == $currentYear) ? "selected" : "";
                echo "<option value='$i' $selected>$i</option>";
            }
            ?>
        </select>

        <label>Capacity: </label>
        <select id="selectCapacity" style="padding: 6px; margin-right: 15px;">
            <option value="0">All rooms</option>
            <option value="1">1 bed</option>
            <option value="2">2 beds</option>
            <option value="3">3 beds</option>
            <option value="4">4+ beds</option>
        </select>

        <button id="btnUpdate" style="padding: 8px 20px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Update Calendar
        </button>
    </div>

    <div id="dp"></div>
</main>

<script type="text/javascript">
    var dp = new DayPilot.Scheduler("dp");

    dp.eventHtmlHandling = "Enabled";
    dp.eventBarMode = "None"; // Вимикаємо стандартний бар, бо маємо свій кастомний зверху
    dp.allowHtml = true;
    dp.scale = "Day";
    dp.startDate = "<?php echo date('Y-m-01'); ?>";
    dp.days = <?php echo date('t'); ?>;
    dp.cellWidth = 60;
    dp.headerHeight = 40;
    dp.timeHeaders = [
        { "groupBy": "Month", "format": "MMMM yyyy" },
        { "groupBy": "Day", "format": "d" }
    ];

    dp.eventHeight = 75; 
    
    dp.onBeforeEventRender = function(args) {
        var start = new DayPilot.Date(args.data.start);
        var end = new DayPilot.Date(args.data.end);
        var paid = args.data.paid || 0;
        var status = args.data.status || "New";
        var name = args.data.name || args.data.text || "No Name";

        var barColor = "#ccc"; 
        var statusText = status;
        var isConfirmed = (status === "Confirmed");

        // Визначаємо кольори згідно з вашим прикладом
        switch (status) {
            case "New": barColor = "#f39c12"; break;         // Orange
            case "Confirmed": barColor = "#27ae60"; break;   // Green
            case "Arrived": barColor = "#3498db"; break;     // Blue
            case "CheckedOut": barColor = "#7f8c8d"; break;  // Gray
            case "Expired": barColor = "#e74c3c"; break;     // Red
        }

        var dateRange = "(" + start.toString("d/M/yyyy") + " - " + end.toString("d/M/yyyy") + ")";
        
        // Стиль підкреслення для Confirmed
        var underlineStyle = isConfirmed ? "border-bottom: 1px solid #ccc; padding-bottom: 2px; margin-bottom: 4px;" : "";

        args.data.html = 
            "<div style='padding: 8px 5px; height: 100%; position: relative; font-size: 12px; color: #444;'>" +
                // Кольорова лінія зверху картки
                "<div style='position: absolute; top: 0; left: 0; right: 0; height: 4px; background-color: " + barColor + ";'></div>" +
                
                // Рядок 1: Ім'я та Дати
                "<div style='font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>" + 
                    "<strong>" + name + "</strong> " + dateRange + 
                "</div>" +
                
                // Рядок 2: Статус (з підкресленням для Confirmed)
                "<div style='margin-top: 3px; font-size: 11px; color: #666; " + underlineStyle + "'>" + 
                    statusText + 
                "</div>" +
                
                // Рядок 3: Відсоток оплати в кутку
                "<div style='position: absolute; bottom: 5px; right: 8px; color: #999; font-size: 10px;'>" + 
                    "Paid: " + paid + "%" + 
                "</div>" +
            "</div>";
    };

    dp.rowHeaderColumns = [
        { name: "Room",     width: 160 },
        { name: "Capacity", width: 100 },
        { name: "Status",   width: 150 }
    ];

    dp.onBeforeRowHeaderRender = function(args) {
        var r = args.row.data;
        if (!r) return;

        var roomName = r.name.replace("Номер", "Room");
        args.row.columns[0].html = "<strong>" + roomName + "</strong>";
        args.row.columns[1].html = r.capacity + (r.capacity == 1 ? " bed" : " beds");

        var status = (r.status || "").toLowerCase();
        var barClass = "bar-Ready";
        var statusText = r.status || "Ready";

        if (status === "dirty")   { barClass = "bar-Dirty";   statusText = "Dirty"; }
        if (status === "cleanup") { barClass = "bar-Cleanup"; statusText = "Cleanup"; }
        if (status === "ready")   { barClass = "bar-Ready";   statusText = "Ready"; }

        args.row.columns[2].html = '<span class="status-bar ' + barClass + '"></span><strong>' + statusText + '</strong>';
    };

    function loadResources() {
        var capacity = $("#selectCapacity").val() || 0;
        $.ajax({
            url: "backend_rooms.php",
            data: { capacity: capacity },
            dataType: "json",
            success: function(data) {
                dp.resources = data;
                dp.update();
            }
        });
    }

    function loadEvents() {
        var month = $("#selectMonth").val();
        var year  = $("#selectYear").val();
        $.ajax({
            url: "backend_reservations.php",
            data: { month: month, year: year },
            dataType: "json",
            success: function(data) {
                dp.events.list = data;
                dp.update();
            }
        });
    }

    $("#btnUpdate").click(function() {
        var month = $("#selectMonth").val().padStart(2, '0');
        var year  = $("#selectYear").val();
        var startStr = year + "-" + month + "-01";
        var daysInMonth = new Date(year, parseInt(month), 0).getDate();
        dp.startDate = startStr;
        dp.days = daysInMonth;
        dp.update();
        loadResources();
        loadEvents();
        dp.message("Calendar updated");
    });

    dp.onEventClick = function (args) {
        var e = args.e;
        var roomOptions = dp.resources.map(function(item) {
            return { name: item.name, id: item.id };
        });

        var form = [
            {name: 'Full Name', id: 'name', type: 'text'},
            {name: 'Check-in Date', id: 'start', type: 'datetime'},
            {name: 'Check-out Date', id: 'end', type: 'datetime'},
            {name: 'Room Number', id: 'room_id', type: 'select', options: roomOptions},
            {name: 'Status', id: 'status', type: 'select', options: [
                {name: "New", id: "New"},
                {name: "Confirmed", id: "Confirmed"},
                {name: "Arrived", id: "Arrived"},
                {name: "Checked Out", id: "CheckedOut"},
                {name: "Expired", id: "Expired"}
            ]},
            {name: 'Paid (%)', id: 'paid', type: 'number'}
        ];

        var data = {
            name: e.data.name,
            start: e.start(),
            end: e.end(),
            room_id: e.resource(),
            status: e.data.status,
            paid: e.data.paid
        };

        DayPilot.Modal.form(form, data).then(function(modal) {
            if (modal.canceled) return;
            if (confirm("Save changes? (Click 'Cancel' to DELETE)")) {
                $.post("backend_update.php", {
                    id: e.id(),
                    name: modal.result.name,
                    start: modal.result.start.toString(),
                    end: modal.result.end.toString(),
                    room_id: modal.result.room_id,
                    status: modal.result.status,
                    paid: modal.result.paid,
                    action: 'update'
                }, function() {
                    dp.message("Changes saved");
                    loadEvents();
                });
            } else {
                if (confirm("Are you sure you want to delete this booking?")) {
                    $.post("backend_delete.php", {
                        id: e.id()
                    }, function(response) {
                        if (response.status === "OK") {
                            dp.message("Deleted");
                            loadEvents(); 
                        }
                    }, "json");
                }
            }
        });
    };

    dp.onEventMoved = function(args) {
        $.post("backend_move.php", {
            id: args.e.id(),
            newStart: args.newStart.toString(),
            newEnd: args.newEnd.toString(),
            newResource: args.newResource
        }, function(response) {
            if (response.result !== "OK") {
                DayPilot.Modal.alert("Error: " + response.message);
                loadEvents();
            }
        }, "json");
    };

    dp.onTimeRangeSelected = function (args) {
        var form = [
            {name: 'Full Name', id: 'name', type: 'text', default: 'New Guest'},
            {name: 'Status', id: 'status', type: 'select', options: [
                {name: "New", id: "New"},
                {name: "Confirmed", id: "Confirmed"},
                {name: "Arrived", id: "Arrived"}
            ], default: 'New'},
            {name: 'Paid (%)', id: 'paid', type: 'number', default: 0}
        ];

        DayPilot.Modal.form(form, {}).then(function(modal) {
            dp.clearSelection();
            if (modal.canceled) return;
            $.post("backend_create.php", {
                start: args.start.toString(),
                end: args.end.toString(),
                resource: args.resource,
                name: modal.result.name,
                status: modal.result.status,
                paid: modal.result.paid
            }, function(response) {
                if (response.result === "OK") {
                    loadEvents();
                } else {
                    DayPilot.Modal.alert(response.message);
                }
            }, "json");
        });
    };

    dp.init();
    loadResources();
    loadEvents();
</script>

<footer>
    <address style="padding:20px; color:#95a5a6; border-top:1px solid #eee; text-align:center;">
        (c) Автор лабораторної роботи: студент спеціальності ПЗІС, Панченко Валентин Олегович
    </address>
</footer>
</body>
</html>