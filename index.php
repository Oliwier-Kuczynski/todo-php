<?php
  $db_server = "localhost";
  $db_login = "root";
  $db_password = "";
  $db_database = "todo";
  $db_port = "3306";

  $conn = new mysqli($db_server, $db_login, $db_password, $db_database, $db_port);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  if($_SERVER['REQUEST_METHOD'] === "POST") {
    $taskName = $_POST['task-name'];
    $category = $_POST['category'];
    $currentDateTime = date('Y-m-d H:i:s'); 
    $sql = "INSERT INTO tasks (name, category, date) VALUES ('$taskName', '$category', '$currentDateTime')";
    $conn->query($sql);

    header("Location: index.php");
  }

  if($_SERVER['REQUEST_METHOD'] === "DELETE") {
    $record_id = $_GET['record-id'];
    
    $sql = "DELETE FROM tasks WHERE id='$record_id'";
    $conn->query($sql);

    http_response_code(200);
  }

  if($_SERVER['REQUEST_METHOD'] === "PUT") {
    $record_id = $_GET['record-id'];
    
    $sql = "UPDATE tasks SET finished=IF(finished = 0, 1, 0) WHERE id='$record_id'";
    $conn->query($sql);

    http_response_code(200);
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./style.css" />
    <script src="./script.js" defer></script>
    <title>To-Do</title>
  </head>
  <body>
    <div>
      <header class="header">
        <h1>To-Do</h1>
      </header>
      <main>
        <section class="controls-section">
          <div class="flex gap">
            <button class="btn--primary flex gap" data-add-task-btn="">
              <img src="./img/add.svg" alt="add" />
              <p>Nowe Zadanie</p>
            </button>
            <button class="btn--secondary flex gap" data-filter-btn="">
              <img src="./img/filter_list.svg" alt="filter" />
              <p>Filter</p>
            </button>
          </div>

          <form
            action="index.php"
            method="post"
            class="add-task flex bg-gap d-none"
            data-add-task=""
          >
            <div>
              <label for="create-task">Treść:</label>
              <input
                type="text"
                name="task-name"
                id="create-task"
                placeholder="Treść zadania..."
              />
            </div>
            <div>
              <label for="category">Kategoria:</label>
              <select name="category" id="category">
                <option value="1">Szkoła</option>
                <option value="2">Praca</option>
                <option value="3">Hobby</option>
              </select>
            </div>

            <button type="submit" class="btn--primary">Dodaj</button>
          </form>

          <form action="index.php" method="get" class="filters grid bg-gap d-none" data-filters="">
              <fieldset class="grid gap">
                  <div>
                      <label for="finished">Ukończone</label>
                      <input type="checkbox" name="finished" id="finished" <?php echo isset($_GET['finished']) && $_GET['finished'] === "on" ? 'checked' : ''; ?> />
                  </div>
                  <div>
                      <label for="not-finished">Nie Ukończone</label>
                      <input type="checkbox" name="not-finished" id="not-finished" <?php echo isset($_GET['not-finished']) && $_GET['not-finished'] === "on" ? 'checked' : ''; ?> />
                  </div>
                  <div>
                      <label for="school">Szkoła</label>
                      <input type="checkbox" name="school" id="school" <?php echo isset($_GET['school']) && $_GET['school'] === "on" ? 'checked' : ''; ?> />
                  </div>
                  <div>
                      <label for="work">Praca</label>
                      <input type="checkbox" name="work" id="work" <?php echo isset($_GET['work']) && $_GET['work'] === "on" ? 'checked' : ''; ?> />
                  </div>
                  <div>
                      <label for="hobby">Hobby</label>
                      <input type="checkbox" name="hobby" id="hobby" <?php echo isset($_GET['hobby']) && $_GET['hobby'] === "on" ? 'checked' : ''; ?> />
                  </div>
              </fieldset>
              <div>
                  <label for="sort">Sortuj:</label>
                  <select name="sort" id="sort">
                      <option value="date-asc" <?php echo isset($_GET['sort']) && $_GET['sort'] === "date-asc" ? 'selected' : ''; ?>>Data rosnąca</option>
                      <option value="date-desc" <?php echo isset($_GET['sort']) && $_GET['sort'] === "date-desc" ? 'selected' : ''; ?>>Data malejąca</option>
                  </select>
              </div>
              <button type="submit" class="btn--primary">Filtruj</button>
          </form>
        </section>
        <section>
          <ul class="grid gap">
            <?php
              if($_SERVER['REQUEST_METHOD'] === "GET") {
                $finished = isset($_GET['finished']) && $_GET['finished'] === "on" ? "on" : "off";
                $notFinished = isset($_GET['not-finished']) && $_GET['not-finished'] === "on" ? "on" : "off";
                $school = isset($_GET['school']) && $_GET['school'] === "on" ? "on" : "off";
                $work = isset($_GET['work']) && $_GET['work'] === "on" ? "on" : "off";
                $hobby = isset($_GET['hobby']) && $_GET['hobby'] === "on" ? "on" : "off";
                $sort = isset($_GET['sort']) && $_GET['sort'] === "date-desc" ? "date DESC" : "date ASC";


                $sql = "SELECT tasks.id, tasks.name as task_name, categories.name AS category_name, tasks.finished, date, color, tasks.category FROM tasks
                INNER JOIN categories ON tasks.category = categories.id";

                if($finished == "on" && $notFinished == "on") {
                  $sql .= " WHERE tasks.finished IN (0, 1)";
                } else if($finished == "on") {
                  $sql .= " WHERE tasks.finished = 1";
                } else if($notFinished == "on") {
                  $sql .= " WHERE tasks.finished = 0";
                }

                if($school == "on" && $work == "on" && $hobby == "on") {
                  $sql .= " AND tasks.category IN (1, 2, 3)";
                } else if($school == "on" && $work == "on") {
                  $sql .= " AND tasks.category IN (1, 2)";
                } else if($school == "on" && $hobby == "on") {
                  $sql .= " AND tasks.category IN (1, 3)";
                } else if($work == "on" && $hobby == "on") {
                  $sql .= " AND tasks.category IN (2, 3)";
                } else if($school == "on") {
                  $sql .= " AND tasks.category = 1";
                } else if($work == "on") {
                  $sql .= " AND tasks.category = 2";
                } else if($hobby == "on") {
                  $sql .= " AND tasks.category = 3";
                }

                $sql .= " ORDER BY {$sort}";

                $result = $conn->query($sql);
                
                if($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                    $taskFinishedClass = ($row["finished"] == 1) ? "task--completed" : "";
                    $taskFinishedCheckbox = ($row["finished"] == 1) ? "checked" : "";
                    
                    echo<<<END
                      <li class="task flex bg-gap {$taskFinishedClass}" style="--task-color: {$row["color"]};" data-id="{$row["id"]}" data-task="">
                      <input type="checkbox" data-task-check="" {$taskFinishedCheckbox} />
                      <div class="grid gap">
                        <h2>{$row["task_name"]}</h2>
                        <div class="calendar-box">
                          <img src="./img/calendar.svg" alt="calendar" />
                          <data>{$row["date"]}</data>
                        </div>
                        <p>{$row["category_name"]}</p>
                      </div>
                    </li>
                    END;
                  }
                }

                mysqli_free_result($result);
                mysqli_close($conn);
              }
            ?>
          </ul>
        </section>
      </main>
    </div>
    <ul class="context-menu d-none" data-context-menu="">
      <!-- <li data-edit-task="">Edytuj</li> -->
      <li data-delete-task="" class="flex gap">
        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
        <p>Usuń</p>
      </li>
    </ul>
  </body>
</html>
