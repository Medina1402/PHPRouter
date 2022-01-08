<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Tasks</h1>
    <input type="text" id="task_description">
    <button id="createTask">Create Task</button>
    <button id="deleteAllTasks">Delete all Tasks</button>

    <?php
        if(sizeof($props["tasks"])) foreach ($props["tasks"] as $task) {
            echo "<li><a href=".str_replace(" ", "_", $task)."> $task </a></li>";
        }
        else echo "<h2>No Tasks</h2>";
    ?>

    <script>
        const $ = element => document.querySelector(element)
        let task_description = $("#task_description")

        const CreateTask = () => {
            let urlSearchParams = new URLSearchParams()
            urlSearchParams.append("task", task_description.value)
            fetch("/create", {method: "POST", body: urlSearchParams}).then(() => window.location.reload())
        }

        task_description.onkeyup = (event) => (event.key === "Enter") ?CreateTask() :null
        $("#createTask").onclick = CreateTask
        $("#deleteAllTasks").onclick = () => fetch("/delete", {method: "DELETE"}).then(() => window.location.reload())
    </script>
</body>
</html>