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

          <form
            action=""
            method="get"
            class="filters grid bg-gap d-none"
            data-filters=""
          >
            <fieldset class="grid gap">
              <div>
                <label for="finished">Ukończone</label>
                <input type="checkbox" name="finished" id="finished" />
              </div>
              <div>
                <label for="not-finished">Nie Ukończone</label>
                <input type="checkbox" name="not-finished" id="not-finished" />
              </div>
              <div>
                <label for="school">Szkoła</label>
                <input type="checkbox" name="school" id="school" />
              </div>
              <div>
                <label for="work">Praca</label>
                <input type="checkbox" name="work" id="work" />
              </div>
              <div>
                <label for="hobby">Hobby</label>
                <input type="checkbox" name="hobby" id="hobby" />
              </div>
            </fieldset>
            <div>
              <label for="sort">Sortuj:</label>
              <select name="sort" id="sort">
                <option value="date-desc">Data malejąca</option>
                <option value="date-asc">Data rosnąca</option>
              </select>
            </div>
            <button type="submit" class="btn--primary">Filtruj</button>
          </form>
        </section>
        <section>
          <ul class="grid gap">
            <?php
              if($_SERVER['REQUEST_METHOD'] === "GET") {
                $sql = "SELECT tasks.id, tasks.name as task_name, categories.name AS category_name, tasks.finished, date, color FROM tasks
                INNER JOIN categories ON tasks.category = categories.id";

                $result = $conn->query($sql);
                
                if($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                    echo<<<END
                      <li class="task flex bg-gap" style="--task-color: {$row["color"]};" data-id="{$row["id"]}">
                      <input type="checkbox" />
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
        <img src="" alt="">
        <p>Usuń</p>
      </li>
    </ul>
  </body>
</html>
